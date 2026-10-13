<?php

namespace App;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentSubmission
{
    /**
     * @param  array{amount: numeric-string|float|int, payment_method: string, reference_number?: ?string, payment_date: mixed, notes?: ?string, proof_image_path?: ?string, processed_by?: ?int}  $details
     */
    public static function submit(Order $order, array $details, ?Payment $payment = null): Payment
    {
        $details['payment_date'] = Carbon::parse($details['payment_date'])->toDateString();
        try {
            return DB::transaction(function () use ($order, $details, $payment): Payment {
                if ($details['payment_method'] === 'GCash' && ! GCashAccount::details()['available']) {
                    throw ValidationException::withMessages(['payment_method' => 'GCash payment is currently unavailable']);
                }
                $order = Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
                abort_if($order->status === 'Cancelled', 403, 'Cancelled orders cannot receive payments.');
                if ($payment !== null) {
                    $payment = Payment::whereKey($payment->id)->lockForUpdate()->firstOrFail();
                    abort_unless($payment->status === 'Rejected', 403, 'Only rejected payments can be corrected.');
                }

                $reserved = $order->payments()->whereIn('status', ['Pending', 'Confirmed'])
                    ->when($payment !== null, fn (Builder $query): Builder => $query->whereKeyNot($payment->id))->sum('amount');
                if ((int) round($details['amount'] * 100) > (int) round(($order->total - $reserved) * 100)) {
                    throw ValidationException::withMessages(['amount' => 'The payment exceeds the remaining balance, including payments awaiting approval.']);
                }

                $reference = trim($details['reference_number'] ?? '');
                $method = $details['payment_method'];
                $duplicate = Payment::query()->when($payment !== null, fn (Builder $query): Builder => $query->whereKeyNot($payment->id));
                if ($method !== 'Cash' && $reference !== '') {
                    $duplicate->where('payment_method', $method)->whereRaw('LOWER(reference_number) = ?', [strtolower($reference)]);
                    $key = hash('sha256', $method.'|'.strtolower($reference));
                } else {
                    $duplicate->where('order_id', $order->id)->where('payment_method', $method)
                        ->where('amount', $details['amount'])->where('reference_number', $reference !== '' ? $reference : null)
                        ->whereDate('payment_date', $details['payment_date']);
                    $key = hash('sha256', $order->id.'|'.$method.'|'.$reference.'|'.number_format($details['amount'], 2, '.', '').'|'.$details['payment_date']);
                }
                if ($duplicate->exists()) {
                    throw ValidationException::withMessages(['reference_number' => 'This payment was already submitted. Correct its existing rejected record instead of submitting it again.']);
                }

                $attributes = array_merge($details, [
                    'order_id' => $order->id, 'status' => 'Pending', 'reference_number' => $reference !== '' ? $reference : null,
                    'submission_key' => $key, 'rejection_reason' => null, 'reviewed_by' => null, 'reviewed_at' => null,
                ]);
                if ($payment !== null) {
                    $payment->update($attributes);

                    return $payment;
                }

                return Payment::create($attributes);
            }, 3);
        } catch (UniqueConstraintViolationException $exception) {
            throw ValidationException::withMessages(['reference_number' => 'This payment was already submitted.']);
        }
    }
}
