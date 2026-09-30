<div class="table-wrap"><table><thead><tr><th>Order</th><th>Customer</th><th>Service</th><th>Status</th><th>Total</th></tr></thead><tbody>
@forelse($orders as $order)<tr><td><a href="{{ route('staff.orders.show', $order) }}">#LVE-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</a></td><td>{{ $order->customer->name ?? 'N/A' }}</td><td>{{ $order->service->service_name ?? 'N/A' }}</td><td><span class="pill">{{ $order->status }}</span></td><td>₱{{ number_format($order->total, 2) }}</td></tr>@empty<tr><td colspan="5" class="empty">No orders to show.</td></tr>@endforelse
</tbody></table></div>
