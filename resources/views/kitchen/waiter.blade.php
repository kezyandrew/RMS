@extends('layouts.layout')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/kitchen_view.css') }}">
    <link rel="stylesheet" href="{{ asset('css/labels.css') }}">
@endpush

@section("content")
<waiterviewcomponent :is_waiter="{{ json_encode($is_waiter) }}" :users="{{ json_encode($users) }}" :store_slack="{{ json_encode($store_slack) }}"></waiterviewcomponent>
@endsection