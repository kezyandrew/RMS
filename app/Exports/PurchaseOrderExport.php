<?php

namespace App\Exports;

use App\Models\PurchaseOrder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\PurchaseOrderResource;

use Carbon\Carbon;

class PurchaseOrderExport implements FromCollection, WithMapping, WithHeadings
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
        

        $query = PurchaseOrder::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('purchase_orders.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('purchase_orders.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('purchase_orders.status', $status);
        }

        $purchase_orders = $query->get();

        return $purchase_orders;
    }

    public function headings(): array
    {
        return [
            'PO NUMBER',
            'PO REFERENCE',
            'ORDER DATE',
            'ORDER DUE DATE',
            'SUPPLIER CODE',
            'SUPPLIER NAME',
            'SUPPLIER ADDRESS',
            'CURRENCY NAME',
            'CURRENCY CODE',
            'SUBTOTAL EXCLUDING TAX',
            'TOTAL DISCOUNT AMOUNT',
            'TOTAL AFTER DISCOUNT',
            'TOTAL TAX AMOUNT',
            'SHIPPING CHARGE',
            'PACKING CHARGE',
            'TOTAL ORDER AMOUNT',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($order): array
    {
        $order = collect(new PurchaseOrderResource($order));
        return [
            (isset($order['po_number']))?$order['po_number']:'',
            (isset($order['po_reference']))?$order['po_reference']:'',
            (isset($order['order_date']))?$order['order_date']:'',
            (isset($order['order_due_date']))?$order['order_due_date']:'',
            (isset($order['supplier_code']))?$order['supplier_code']:'',
            (isset($order['supplier_name']))?$order['supplier_name']:'',
            (isset($order['supplier_address']))?$order['supplier_address']:'',
            (isset($order['currency_name']))?$order['currency_name']:'',
            (isset($order['currency_code']))?$order['currency_code']:'',
            (isset($order['subtotal_excluding_tax']))?$order['subtotal_excluding_tax']:'',
            (isset($order['total_discount_amount']))?$order['total_discount_amount']:'',
            (isset($order['total_after_discount']))?$order['total_after_discount']:'',
            (isset($order['total_tax_amount']))?$order['total_tax_amount']:'',
            (isset($order['shipping_charge']))?$order['shipping_charge']:'',
            (isset($order['packing_charge']))?$order['packing_charge']:'',
            (isset($order['total_order_amount']))?$order['total_order_amount']:'',
            (isset($order['status']['label']))?$order['status']['label']:'',
            (isset($order['created_at_label']))?$order['created_at_label']:'',
            (isset($order['created_by']['fullname']))?$order['created_by']['fullname']:'',
            (isset($order['updated_at_label']))?$order['updated_at_label']:'',
            (isset($order['updated_by']['fullname']))?$order['updated_by']['fullname']:''
        ];
    }
}
