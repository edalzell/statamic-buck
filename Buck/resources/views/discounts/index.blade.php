@extends('layout')

@section('content')

    <div class="flex items-center mb-3">
        <h1 class="flex-1">Discounts</h1>
        <a href="{{ route('discounts.create') }}" class="btn btn-primary">Create Discount</a>
    </div>
    <div class="card flush dossier">
        <div class="dossier-table-wrapper">
            <table class="dossier">
                <thead>
                    <tr>
                        <th class="column-id">ID</th>
                        <th class="column-customer">Customer ID</th>
                        <th class="column-discount-id">Discount ID</th>
                        <th class="column-discount">Discount</th>
                        <th class="column-date">Created At</th>
                        <th class="column-date">Updated At</th>
                        <th class="column-date">Completed At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discounts as $discount)
                    <tr>
                        <td class="cell-title first-cell">
                            <span class="column-label">ID</span>
                            <a class="" href="{{ route('orders.edit', ['order' => $order->id]) }}">{{ $order->id }}</a>
                        </td>
                        <td class="cell-customer">
                            <span>{{ $order->customer->name }}</span>
                        </td>
                        <td class="cell-discount-id">
                            <span>{{ $order->discount_id }}</span>
                        </td>
                        <td class="cell-discount">
                            <span>{{ $order->discount }}</span>
                        </td>
                        <td class="cell-date">{{ isset($order->created_at) ? $order->created_at->format('Y/m/d') : '' }}</td>
                        <td class="cell-date">{{ isset($order->updated_at) ? $order->updated_at->format('Y/m/d') : '' }}</td>
                        <td class="cell-date">{{ isset($order->completed_at) ? $order->completed_at->format('Y/m/d') : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
