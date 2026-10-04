<div class="table-wrap"><table>
    <thead><tr><th>Order</th><th>Service</th><th>Date</th><th>Status</th><th>Payment Status</th><th>Total (₱)</th></tr></thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td><a href="{{ route('customer.orders.show', $order) }}">#{{ $order->id }}</a></td>
                <td>{{ $order->service?->service_name ?? 'Unavailable service' }}</td>
                <td>{{ $order->order_date?->format('M d, Y') ?? '—' }}</td>
                <td><span class="pill">{{ $order->status }}</span></td>
                <td><span class="pill">{{ $order->payment_status }}</span></td>
                <td>₱{{ number_format($order->total, 2) }}</td>
            </tr>
        @empty
            <tr><td colspan="6" class="empty">No orders yet. Your laundry orders will appear here.</td></tr>
        @endforelse
    </tbody>
</table></div>
