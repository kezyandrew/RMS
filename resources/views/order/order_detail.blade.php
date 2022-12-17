@extends('layouts.layout')

@section("content")
<orderdetailcomponent :order_data="{{ json_encode($order_data) }}" :delete_order_access="{{ json_encode($delete_order_access) }}" :share_invoice_sms_access="{{ json_encode($share_invoice_sms_access) }}" :merge_order_access="{{ json_encode($merge_order_access) }}" :unmerge_order_access="{{ json_encode($unmerge_order_access) }}" :print_order_link="{{ json_encode($print_order_link) }}"></orderdetailcomponents>
@endsection