<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\Role as RoleModel;

use App\Models\Supplier as SupplierModel;
use App\Models\Category as CategoryModel;
use App\Models\Taxcode as TaxcodeModel;
use App\Models\TaxcodeType as TaxcodeTypeModel;
use App\Models\Discountcode as DiscountcodeModel;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\Account as AccountModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Store as StoreModel;

class Report extends Controller
{
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_DOWNLOAD_REPORT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        //user
        $data['user_statuses'] = MasterStatus::select('value', 'label')->filterByKey('USER_STATUS')->active()->sortValueAsc()->get();

        $data['roles'] = RoleModel::select('slack', 'role_code', 'label')->resolveSuperAdminRole()->active()->sortLabelAsc()->get();

        //product
        $data['product_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PRODUCT_STATUS')->active()->sortValueAsc()->get();

        $data['suppliers'] = SupplierModel::select('slack', 'supplier_code', 'name')->sortNameAsc()->get();

        $data['categories'] = CategoryModel::select('slack', 'category_code', 'label')->sortLabelAsc()->get();

        $data['taxcodes'] = TaxcodeModel::select('slack', 'tax_code', 'label')->sortLabelAsc()->get();

        $data['discountcodes'] = DiscountcodeModel::select('slack', 'discount_code', 'label')->sortLabelAsc()->get();

        //order
        $data['order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('ORDER_STATUS')->active()->sortValueAsc()->get();

        //purchase order
        $data['purchase_order_statuses'] = MasterStatus::select('value', 'label')->filterByKey('PURCHASE_ORDER_STATUS')->active()->sortValueAsc()->get();

        //store
        $data['store_statuses'] = MasterStatus::select('value', 'label')->filterByKey('STORE_STATUS')->active()->sortValueAsc()->get();

        //customer
        $data['customer_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CUSTOMER_STATUS')->active()->sortValueAsc()->get();

        //category
        $data['category_statuses'] = MasterStatus::select('value', 'label')->filterByKey('CATEGORY_STATUS')->active()->sortValueAsc()->get();

        //supplier
        $data['supplier_statuses'] = MasterStatus::select('value', 'label')->filterByKey('SUPPLIER_STATUS')->active()->sortValueAsc()->get();

        //tax code
        $data['taxcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('TAX_CODE_STATUS')->active()->sortValueAsc()->get();

        //discount code
        $data['discountcode_statuses'] = MasterStatus::select('value', 'label')->filterByKey('DISCOUNTCODE_STATUS')->active()->sortValueAsc()->get();
        
        //invoice code
        $data['invoice_statuses'] = MasterStatus::select('value', 'label')->filterByKey('INVOICE_STATUS')->active()->sortValueAsc()->get();

        //quotation code
        $data['quotation_statuses'] = MasterStatus::select('value', 'label')->filterByKey('QUOTATION_STATUS')->active()->sortValueAsc()->get();

        //transaction
        $data['transaction_types'] = MasterTransactionTypeModel::select('transaction_type_constant', 'label')->active()->get();
        $data['accounts'] = AccountModel::select('accounts.slack', 'accounts.label', 'master_account_type.label as account_type_label')->masterAccountTypeJoin()->active()->get();
        $data['payment_methods'] = PaymentMethodModel::select('slack', 'label')->active()->get();

        return view('report.report', $data);
    }

    
    public function best_seller_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_BEST_SELLER';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.best_seller_report', $data);
    }

    public function day_wise_sale_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_DAY_WISE_SALE';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.day_wise_sale_report', $data);
    }

    public function catgeory_report(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_CATEGORY_REPORT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();

        return view('report.catgeory_report', $data);
    }

    public function product_quantity_alert(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_PRODUCT_QUANTITY_ALERT';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        return view('report.product_quantity_alert', $data);
    }

    public function store_stock_chart(Request $request){
        //check access
        $data['menu_key'] = 'MM_REPORT';
        $data['sub_menu_key'] = 'SM_STORE_STOCK_CHART';
        check_access(array($data['menu_key'],$data['sub_menu_key']));

        $data['store'] = StoreModel::select('currency_name', 'currency_code')
        ->where('id', $request->logged_user_store_id)
        ->first();
        
        return view('report.store_stock_chart', $data);
    }
}
