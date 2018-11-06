@extends('layout')

@section('content')

    <script>
        Statamic.Publish = {
            contentData: {!! json_encode($data) !!},
        };
    </script>

    <publish
        fieldset-name="discount"
        id="{{ $discount->id }}"
        title="{{ $discount->id }}"
        submit-url="{{ route('discounts.update', ['discount' => $discount]) }}"
    ></publish>

@endsection
