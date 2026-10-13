@once
<style>
.record-icon-actions {
    display: flex;
    align-items: center;
    gap: 7px;
}
.record-icon-actions .record-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 34px;
    height: 34px;
    padding: 0;
    border: 0;
    border-radius: 7px;
    text-decoration: none;
    cursor: pointer;
}
.record-icon-actions .record-action svg {
    width: 16px;
    height: 16px;
}
.record-icon-actions .record-edit {
    background: #f0eaff;
    color: #7545d0;
}
.record-icon-actions .record-delete {
    background: #fff0f1;
    color: #e05263;
}
.record-icon-actions .record-action:hover {
    filter: brightness(.96);
}
.record-icon-actions .record-action:focus-visible {
    outline: 2px solid #4169c8;
    outline-offset: 3px;
}
</style>
@endonce
<div class="table-wrap"><table><thead><tr><th>Record</th><th>Customer</th><th>Service</th><th>Status</th><th>Total</th><th>Actions</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><a href="{{ route('staff.orders.show', $order) }}">#LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</a></td><td>{{ $order->customer->name ?? 'N/A' }}</td><td>{{ $order->service->service_name ?? 'N/A' }}</td><td><span class="pill" data-status="{{ $order->status }}">{{ $order->status }}</span></td><td>₱{{ number_format($order->total, 2) }}</td><td><div class="record-icon-actions"><a class="record-action record-edit" href="{{ route('staff.orders.edit', $order) }}" aria-label="Edit record {{ $order->id }}" title="Edit record"><i data-lucide="pencil" aria-hidden="true"></i></a><form method="POST" action="{{ route('staff.orders.destroy', $order) }}" onsubmit="return confirm('Delete this record and its related data?')">@csrf @method('DELETE')<button type="submit" class="record-action record-delete" aria-label="Delete record {{ $order->id }}" title="Delete record"><i data-lucide="trash-2" aria-hidden="true"></i></button></form></div></td></tr>@empty<tr><td colspan="6" class="empty">No records to show.</td></tr>@endforelse
</tbody></table></div>
