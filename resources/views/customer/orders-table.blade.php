<div class="table-wrap"><table>
    <thead><tr><th>Order</th><th>Service</th><th>Date</th><th>Status</th><th>Payment Status</th><th>Total (₱)</th><th>Actions</th></tr></thead>
    <tbody>
        @forelse($orders as $order)
            <tr>
                <td><a href="{{ route('customer.orders.show', $order) }}">#{{ $order->id }}</a></td>
                <td>{{ $order->service?->service_name ?? 'Unavailable service' }}</td>
                <td>{{ $order->order_date?->format('M d, Y') ?? '—' }}</td>
                <td><span class="pill" data-status="{{ $order->status }}">{{ $order->status }}</span></td>
                <td><span class="pill" data-status="{{ $order->payment_status }}">{{ $order->payment_status }}</span></td>
                <td>₱{{ number_format($order->total, 2) }}</td>
                <td>
                    @if($order->canCustomerModify())
                        <div class="record-icon-actions">
                            <a class="record-action record-edit" href="{{ route('customer.orders.edit', $order) }}" title="Edit order" aria-label="Edit order {{ $order->id }}"><i data-lucide="pencil" aria-hidden="true"></i></a>
                            <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" onsubmit="return confirm('Cancel this order?')">
                                @csrf
                                @method('PATCH')
                                <button class="record-action record-delete" type="submit" title="Cancel order" aria-label="Cancel order {{ $order->id }}"><i data-lucide="circle-x" aria-hidden="true"></i></button>
                            </form>
                        </div>
                    @else
                        <span class="muted">&mdash;</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr><td colspan="7" class="empty">No orders yet. Your laundry orders will appear here.</td></tr>
        @endforelse
    </tbody>
</table></div>
