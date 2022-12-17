<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\OrderResource;

use Carbon\Carbon;

class OrderExport implements FromCollection, WithMapping, WithHeadings
{
    use Exportable;
    
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function collection()
    {
        $from_created_date = $this->data['from_created_date'];
        $to_created_date = $this->data['to_created_date'];
        $status = $this->data['status'];
        

        $query = Order::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('orders.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('orders.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('orders.status', $status);
        }

        $orders = $query->get();

        return $orders;
    }

    public function headings(): array
    {
        return [
            'ORDER NUMBER',
            'CUSTOMER PHONE',
            'CUSTOMER EMAIL',
            'ORDER LEVEL DISCOUNT CODE',
            'ORDER LEVEL DISCOUNT PERCENTAGE',
            'ORDER LEVEL DISCOUNT AMOUNT',
            'PRODUCT LEVEL TOTAL DISCOUNT',
            'ORDER LEVEL TAX CODE',
            'ORDER LEVEL TAX PERCENTAGE',
            'ORDER LEVEL TAX AMOUNT',
            'ORDER LEVEL TAX COMPONENTS',
            'PRODUCT LEVEL TOTAL TAX',
            'PURCHASE AMOUNT SUBTOTAL EXCLUDING TAX',
            'SALE AMOUNT SUBTOTAL EXCLUDING TAX',
            'TOTAL DISCOUNT AMOUNT',
            'TOTAL AFTER DISCOUNT',
            'TOTAL TAX AMOUNT',
            'TOTAL ORDER AMOUNT',
            'PAYMENT METHOD',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($order): array
    {
        $order = collect(new OrderResource($order));
        return [
            (isset($order['order_number']))?$order['order_number']:'',
            (isset($order['customer_phone']))?$order['customer_phone']:'',
            (isset($order['customer_email']))?$order['customer_email']:'',
            (isset($order['order_level_discount_code']))?$order['order_level_discount_code']:'',
            (isset($order['order_level_discount_percentage']))?$order['order_level_discount_percentage']:'',
            (isset($order['order_level_discount_amount']))?$order['order_level_discount_amount']:'',
            (isset($order['product_level_total_discount']))?$order['product_level_total_discount']:'',
            (isset($order['order_level_tax_code']))?$order['order_level_tax_code']:'',
            (isset($order['order_level_tax_percentage']))?$order['order_level_tax_percentage']:'',
            (isset($order['order_level_tax_amount']))?$order['order_level_tax_amount']:'',
            (isset($order['order_level_tax_components']))?$order['order_level_tax_components']:'',
            (isset($order['product_level_total_tax']))?$order['product_level_total_tax']:'',
            (isset($order['purchase_amount_subtotal_excluding_tax']))?$order['purchase_amount_subtotal_excluding_tax']:'',
            (isset($order['sale_amount_subtotal_excluding_tax']))?$order['sale_amount_subtotal_excluding_tax']:'',
            (isset($order['total_discount_amount']))?$order['total_discount_amount']:'',
            (isset($order['total_after_discount']))?$order['total_after_discount']:'',
            (isset($order['total_tax_amount']))?$order['total_tax_amount']:'',
            (isset($order['total_order_amount']))?$order['total_order_amount']:'',
            (isset($order['payment_method']))?$order['payment_method']:'',
            (isset($order['status']['label']))?$order['status']['label']:'',
            (isset($order['created_at_label']))?$order['created_at_label']:'',
            (isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'',
            (isset($order['updated_at_label']))?$order['updated_at_label']:'',
            (isset($order['updated_by']['fullname']))?$order['updated_by']['fullname']:''
        ];
    }
}
