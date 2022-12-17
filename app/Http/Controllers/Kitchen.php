<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\MasterStatus;
use App\Models\Store as StoreModel;
use App\Models\Role as RoleModel;
use App\Models\User as UserModel;

use App\Http\Resources\UserResource;

class Kitchen extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_KITCHEN';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['kitchen_statuses'] = MasterStatus::select('value_constant', 'label')->filterByKey('ORDER_KITCHEN_STATUS')->active()->sortValueAsc()->get();
        
        $data['change_kitchen_order_status'] = check_access(['A_CHANGE_KITCHEN_ORDER_STATUS'] ,true);

        $data['pos_order_edit'] = check_access(['A_EDIT_ORDER'] ,true);

        $data['store_slack'] = $request->logged_user_store_slack;
        
        return view('kitchen.kitchen', $data);
    }

    public function waiter(Request $request){
        //check access
        $data['menu_key'] = 'MM_RESTAURANT';
        $data['sub_menu_key'] = 'SM_RESTAURANT_WAITER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $is_waiter = false;

        $store_data = StoreModel::select('id', 'restaurant_waiter_role_id')
        ->where([
            ['id', '=', $request->logged_user_store_id],
            ['status', '=', 1]
        ])
        ->first();

        $data['users'] = [];
        if (!empty($store_data)) {
            if($request->logged_user_role_id != $store_data->restaurant_waiter_role_id){
                
                $is_waiter = false;

                if ($store_data->restaurant_waiter_role_id != ''){
                    $waiter_role_id = RoleModel::select('id')
                    ->where('id', '=', $store_data->restaurant_waiter_role_id)
                    ->active()
                    ->first();
                    
                    $user_list = UserModel::select('*', 'user_stores.id as user_store_access')
                    ->hideSuperAdminRole()
                    ->userStoreAccessData()
                    ->active()
                    ->where('role_id', '=', $waiter_role_id->id)
                    ->where('user_stores.store_id', $store_data->id)
                    ->whereNotNull('user_stores.id')
                    ->groupBy('users.id')
                    ->get();
                    
                    $users = UserResource::collection($user_list);
                    $data['users'] = $users;
                }     
            }else{
                $is_waiter = true;
            }
        }

        $data['is_waiter'] = $is_waiter;

        $data['store_slack'] = $request->logged_user_store_slack;
        
        return view('kitchen.waiter', $data);
    }
}
