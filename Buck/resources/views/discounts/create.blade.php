@extends('layout')

@section('content')

    <script>
        Statamic.Publish = {
            contentData: {!! json_encode($data) !!},
        };
    </script>

    <publish
        fieldset-name="discount"
        :is-new="true"
        title="New Discount"
        submit-url="{{ route('discounts.store') }}"
    ></publish>

@endsection