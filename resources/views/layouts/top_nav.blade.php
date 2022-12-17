@php
    $navbar_logo = config("app.navbar_logo");
    $user_slack = session('slack');
@endphp

<topnavcomponent :user_slack="{{ json_encode($user_slack) }}" :logged_user_data="{{ json_encode($logged_user_data) }}" :navbar_logo="{{ json_encode($navbar_logo) }}" :quick_links="{{ json_encode($quick_links) }}" :order_page="{{ json_encode(isset($order)?true:false)}}"></topnavcomponent>