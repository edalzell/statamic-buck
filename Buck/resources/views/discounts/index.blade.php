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
                        <th class="column-type">Type</th>
                        <th class="column-limit-type">Limit Type</th>
                        <th class="column-limit">Limit</th>
                        <th class="column-amount">Amount</th>
                        <th class="column-created-at">Created At</th>
                        <th class="column-updated-at">Updated At</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($discounts as $discount)
                    <tr>
                        <td class="cell-title first-cell">
                            <span class="column-label">ID</span>
                            <a class="" href="{{ route('discounts.edit', ['discount' => $discount->id]) }}">{{ $discount->id }}</a>
                        </td>
                        <td class="cell-type">
                            <span>{{ $discount->type }}</span>
                        </td>
                        <td class="cell-limit-type">
                            <span>{{ $discount->limit_type }}</span>
                        </td>
                        <td class="cell-limit">
                            <span>{{ $discount->limit }}</span>
                        </td>
                        <td class="cell-amount">
                            <span>{{ $discount->amount }}</span>
                        </td>
                        <td class="cell-date">{{ isset($discount->created_at) ? $discount->created_at->format('Y/m/d') : '' }}</td>
                        <td class="cell-date">{{ isset($discount->updated_at) ? $discount->updated_at->format('Y/m/d') : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
