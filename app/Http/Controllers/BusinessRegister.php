<?php

namespace App\Http\Controllers;

use App\Models\MasterStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\BusinessRegister as BusinessRegisterModel;
use App\Models\BillingCounter as BillingCounterModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Order as OrderModel;

use App\Http\Resources\BusinessRegisterResource;

use Mpdf\Mpdf;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class BusinessRegister extends Controller
{
    //This is the function that loads the listing page
    public function index(Request $request){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_BUSINESS_REGISTERS';
        check_access(array($data['menu_key'],$data['sub_menu_key']));
        
        return view('business_register.business_registers', $data);
    }

    //This is the function that loads the add/edit page
    public function add_business_register(){
        //check access
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_BUSINESS_REGISTERS';
        $data['action_key'] = 'A_ADD_ORDER';
        check_access(array($data['action_key']));

        $data['billing_counters'] = $this->get_free_billing_counters();

        return view('business_register.add_business_register', $data);
    }

    //This is the function that loads the detail page
    public function detail($slack){
        $data['menu_key'] = 'MM_ACCOUNT';
        $data['sub_menu_key'] = 'SM_BUSINESS_REGISTERS';
        $data['action_key'] = 'A_DETAIL_BUSINESS_REGISTER';
        check_access([$data['action_key']]);

        $business_register = BusinessRegisterModel::where('slack', '=', $slack)->first();
        
        if (empty($business_register)) {
            abort(404);
        }

        $business_register_data = new BusinessRegisterResource($business_register);
        
        $data['business_register_data'] = $business_register_data;

        $data['delete_register_access'] = check_access(['A_DELETE_BUSINESS_REGISTER'] ,true);

        $data['print_register_report_link'] = route('print_register_report', ['slack' => $slack]);

        return view('business_register.business_register_detail', $data);
    }

    public function get_free_billing_counters(){
        $available_counters = [];

        $billing_counters = BillingCounterModel::select('id', 'billing_counters.slack', 'billing_counter_code', 'counter_name')
        ->active()
        ->get();

        foreach($billing_counters as $billing_counter){
            
            $counter_occupied = BusinessRegisterModel::select('id')
            ->where('billing_counter_id', '=', $billing_counter->id)
            ->whereNull('closing_date')
            ->get()->count();

            if ($counter_occupied == 0) {
                $available_counters[] = $billing_counter;
            }
        }

        return $available_counters;
    }

    public function print_register_report(Request $request, $slack = '', $type = 'INLINE'){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);

        $business_register_query = BusinessRegisterModel::select('slack')
        ->when($slack == '', function ($business_register_query) use ($request) {
            $business_register_query->where('user_id', '=', trim($request->logged_user_id));
            $business_register_query->whereNull('closing_date');
        })
        ->when($slack != '', function ($business_register_query) use ($slack) {
            $business_register_query->where('slack', '=', trim($slack));
        });
        $business_register_data = $business_register_query->first();
        
        if (empty($business_register_data)) {
            throw new Exception("You dont have any register open", 400);
        }

        $business_register_slack = $business_register_data->slack;
        $business_register_details = $this->business_register_report_data($business_register_slack);

        $date = Carbon::now();
        $current_date = $date->format('d-m-Y H:i');
        $store = $request->logged_user_store_code.'-'.$request->logged_user_store_name;
        $currency = $request->logged_user_store_currency;
        
        $view_file = 'business_register.report.business_register_report_print';
        $css_file = 'css/business_register_report.css';
        $print_data = view($view_file, ['data' => $business_register_details->toArray(), 'store' => $store, 'date' => $current_date, 'currency' => $currency])->render();
       
        $mpdf_config = [
            'mode'          => 'utf-8',
            'format'        => 'a4',
            'orientation'   => 'P',
            'margin_left'   => 3,
            'margin_right'  => 3,
            'margin_top'    => 3,
            'margin_bottom' => 3,
            'tempDir' => storage_path()."/pdf_temp" 
        ];

        $stylesheet = File::get(public_path($css_file));
        $mpdf = new Mpdf($mpdf_config);
        $mpdf->SetDisplayMode('real');
        $mpdf->showImageErrors = true;
        $mpdf->WriteHTML($stylesheet, \Mpdf\HTMLParserMode::HEADER_CSS);
        $mpdf->WriteHTML($print_data);
        $mpdf->SetHTMLFooter('<div class="footer">store: '.$store.' | generated on: '.$current_date.' | page: {PAGENO}/{nb}</div>');

        $filename = 'register_report_'.$business_register_slack.'_'.date('Y_m_d_h_i_s').'.pdf';

        if($type == 'INLINE'){
            $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
        }else{

            Storage::disk('register')->delete(
                [
                    $filename
                ]
            );

            $view_path = Config::get('constants.upload.register.view_path');
            $upload_dir = Storage::disk('register')->getAdapter()->getPathPrefix();

            $mpdf->Output($upload_dir.$filename, \Mpdf\Output\Destination::FILE);

            $download_link = $view_path.$filename;
            return $download_link; 
        }
    }

    public function business_register_report_data($business_register_slack){
        
        $response = [];

        $payment_methods = PaymentMethodModel::select('id', 'label')
        ->active()
        ->get();

        $business_register = BusinessRegisterModel::select('*')
        ->where('slack', '=', $business_register_slack)
        ->first();

        $order_data = [];
        $payment_method_array= [];

        if(!empty($business_register)){
            
            $order_data = OrderModel::select(DB::raw('COUNT(id) as order_count, SUM(total_order_amount) as order_value'))
            ->where('register_id', '=', $business_register->id)
            ->closed()
            ->first();

            foreach($payment_methods as $payment_method){

                $payment_method_order_amount = OrderModel::select(DB::raw('COUNT(id) as order_count, SUM(total_order_amount) as order_value'))
                ->where([
                    ['payment_method_id', '=', $payment_method->id],
                    ['register_id', '=', $business_register->id],
                ])
                ->closed()
                ->first();

                $payment_method_array[] = [
                    'payment_method' => $payment_method['label'],
                    'value' => ($payment_method_order_amount->order_value)?$payment_method_order_amount->order_value:0.00,
                    'order_count' => $payment_method_order_amount->order_count
                ];
            }

        }

        $business_register_resource = new BusinessRegisterResource($business_register);

        $response = collect($business_register_resource)->union(collect(['payment_method_data' => $payment_method_array, 'order_data' => $order_data]));
        
        return $response;
    }

    public function register_summary(Request $request, $slack){
        $data['menu_key'] = 'MM_ORDERS';
        $data['sub_menu_key'] = 'SM_POS_ORDERS';
        check_access([$data['sub_menu_key']]);

        $data['register_data'] = $this->business_register_report_data($slack);

        $data['pdf_print'] = $this->print_register_report($request, $slack, 'FILE');

        $data['new_order_link'] = route('add_order');

        $data['new_order_access'] = check_access(['A_ADD_ORDER'] ,true);

        return view('business_register.business_register_summary', $data);
    }
}
