<?php

namespace App;

use App\Models\Order;
use App\Models\Service;
use App\Models\Staff;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;

class OrderStaffAssignment
{
    /** @return list<array{id: int, name: string, job_position: string, unfinished_orders_count: int}> */
    public function options(Service $service): array
    {
        if (! in_array($service->required_job_position, Staff::LAUNDRY_JOB_POSITIONS, true)) {
            return [];
        }

        return Staff::where('status', 'Active')->where('role', $service->required_job_position)
            ->withCount(['orders as unfinished_orders_count' => fn (Builder $query) => $query->whereNotIn('status', ['Completed', 'Cancelled'])])
            ->orderBy('unfinished_orders_count')->orderBy('id')->get()
            ->map(fn (Staff $worker): array => ['id' => $worker->id, 'name' => $worker->name, 'job_position' => $worker->role, 'unfinished_orders_count' => (int) $worker->unfinished_orders_count])
            ->all();
    }

    public function validateSelection(Service $service, ?int $staffId, string $newStatus, ?Order $order = null): ?int
    {
        if ($order && $order->status !== 'Pending') {
            $errors = [];
            if ($service->id !== (int) $order->service_id) {
                $errors['service_id'] = 'The service cannot be changed after work has started or the order has closed.';
            }
            if ($staffId !== ($order->staff_id === null ? null : (int) $order->staff_id)) {
                $errors['staff_id'] = 'The assigned staff cannot be changed after work has started or the order has closed.';
            }
            if ($newStatus === 'Pending') {
                $errors['status'] = 'An order that has started or closed cannot be reset to Pending to change its assignment.';
            }
            if ($errors) {
                throw ValidationException::withMessages($errors);
            }

            return $staffId;
        }

        if (! in_array($service->required_job_position, Staff::LAUNDRY_JOB_POSITIONS, true)) {
            throw ValidationException::withMessages(['service_id' => 'Configure the required job position for this service before assigning staff.']);
        }
        Staff::where('status', 'Active')->where('role', $service->required_job_position)->lockForUpdate()->get();
        $options = $this->options($service);
        $preserved = $order && $service->id === (int) $order->service_id
            ? collect($options)->firstWhere('id', $order->staff_id)
            : null;
        $assignedStaffId = $preserved['id'] ?? $options[0]['id'] ?? null;
        if ($assignedStaffId === null) {
            throw ValidationException::withMessages(['staff_id' => 'No active '.$service->required_job_position.' is available. Update staff availability before saving.']);
        }
        if ($staffId !== null && $staffId !== $assignedStaffId) {
            throw ValidationException::withMessages(['staff_id' => 'Staff assignment is automatic and cannot be selected manually. Refresh the assignment and try again.']);
        }

        return $assignedStaffId;
    }
}
