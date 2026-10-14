<?php

namespace App\Http\Controllers;

use App\Http\Resources\OrderResource;
use App\Http\Resources\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\Rule;

class OperationalApiController extends Controller
{
    public function orders(Request $request): AnonymousResourceCollection
    {
        $details = $this->filters($request, Order::STATUSES);
        $orders = $this->ordersQuery($request)->with('service')
            ->when(isset($details['status']), fn (Builder $query): Builder => $query->where('status', $details['status']))
            ->latest('id')->paginate($details['per_page'] ?? 15)->withQueryString();

        return OrderResource::collection($orders);
    }

    public function order(Request $request, int $order): OrderResource
    {
        return new OrderResource($this->ordersQuery($request)->with('service')->findOrFail($order));
    }

    public function payments(Request $request): AnonymousResourceCollection
    {
        $details = $this->filters($request, Payment::STATUSES);
        $payments = $this->paymentsQuery($request)
            ->when(isset($details['status']), fn (Builder $query): Builder => $query->where('status', $details['status']))
            ->latest('id')->paginate($details['per_page'] ?? 15)->withQueryString();

        return PaymentResource::collection($payments);
    }

    public function payment(Request $request, int $payment): PaymentResource
    {
        return new PaymentResource($this->paymentsQuery($request)->findOrFail($payment));
    }

    /** @param list<string> $statuses
     * @return array{status?: string, per_page?: int, page?: int}
     */
    private function filters(Request $request, array $statuses): array
    {
        return $request->validate([
            'status' => ['sometimes', 'string', Rule::in($statuses)],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);
    }

    /** @return Builder<Order> */
    private function ordersQuery(Request $request): Builder
    {
        $query = Order::query();
        if ($request->user()->role === 'customer') {
            $query->where('customer_id', $request->user()->customerProfile?->id)->whereNotNull('customer_id');
        }

        return $query;
    }

    /** @return Builder<Payment> */
    private function paymentsQuery(Request $request): Builder
    {
        return Payment::query()->whereIn('order_id', $this->ordersQuery($request)->select('id'));
    }
}
