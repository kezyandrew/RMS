<div class="side-nav pl-sm-0 pl-lg-4 pl-xl-4">
    <ul class="list-unstyled modules">
        @foreach ($menus as $menu_key_id => $menu)
            
            @php $sub_menu_exists = (isset($menu['sub_menu']))?"dropdown-toggle":""; @endphp
            @php $data_toggle_check = (isset($menu['sub_menu']))?'collapse':""; @endphp
            @php $main_route = (isset($menu['route']) && $menu['route']!='')?route($menu['route']):'#'; @endphp
            @php $menu_selected = ($menu['menu_key'] == $menu_key)?"menu-selected selected-menu-bg":""; @endphp
            @php $menu_icon = (isset($menu['icon']))?'<i class="'.$menu['icon'].' main-menu-icon"></i>':''; @endphp
            
            <li class="">
                
                <a href="{{ ($sub_menu_exists == '')? $main_route : '#menu'.$menu_key_id }}"  data-toggle="{{ $data_toggle_check }}" aria-expanded="false" class="{{ $sub_menu_exists }} {{ $menu_selected }} module-parent">
                    <span class="main-menu-text text-truncate">{!! $menu_icon !!} {{ __($menu['label']) }}</span>
                </a>
                
                @isset($menu['sub_menu'])
                @php $submenu_toggle = (isset($menu['sub_menu']) && ($menu['menu_key'] == $menu_key))?'show in':""; @endphp
                <ul class="collapse list-unstyled module {{ $submenu_toggle }}" id="menu{{ $menu_key_id }}" aria-expanded="false">
                    
                    @foreach ($menu['sub_menu'] as $sub_menu)
                    
                    @php $submenu_selected_bg = (isset($sub_menu["menu_key"]) && isset($sub_menu_key))?(($sub_menu["menu_key"] == $sub_menu_key)?"selected-menu-bg":""):""; @endphp
                    @php $submenu_selected = (isset($sub_menu["menu_key"]) && isset($sub_menu_key))?(($sub_menu["menu_key"] == $sub_menu_key)?"submenu-selected":""):""; @endphp
                    @php $route = (isset($sub_menu['route']) && $sub_menu['route']!='')?route($sub_menu['route']):'#'; @endphp
                    
                    <li class="{{ $submenu_selected_bg }}">
                        <a href="{{ $route }}" class="{{ $submenu_selected }} sub-menu-text text-truncate">{{ __($sub_menu['label']) }}</a>
                    </li>
                    
                    @endforeach
                
                </ul>
                @endisset
            
            </li>
        @endforeach
    </ul>
</div>