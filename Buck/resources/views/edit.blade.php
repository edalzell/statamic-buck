@extends('layout')

@section('content')

    <script>
        Statamic.Publish = {
            contentData: {!! json_encode($data) !!},
        };
    </script>

    <publish
        fieldset-name="order"
        id="{{ $order->id }}"
        title="{{ $product->id }}"
        submit-url="{{ route('orders.update', ['order' => $order]) }}"
    ></publish>

@endsection
