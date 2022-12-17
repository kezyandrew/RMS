@extends('layouts.layout')

@section("content")
<userdetailcomponent :user_data="{{ json_encode($user_data) }}"></userdetailcomponent>
@endsection