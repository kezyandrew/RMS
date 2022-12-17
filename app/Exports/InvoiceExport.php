<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\InvoiceResource;

use Carbon\Carbon;

class InvoiceExport implements FromCollection, WithMapping, WithHeadings
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
        

        $query = Invoice::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('invoices.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('invoices.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('invoices.status', $status);
        }

        $invoices = $query->get();

        return $invoices;
    }

    public function headings(): array
    {
        return [
            'BILL TO',
            'INVOICE NUMBER',
            'INVOICE REFERENCE',
            'INVOICE DATE',
            'INVOICE DUE DATE',
            'BILL TO CODE',
            'BILL TO NAME',
            'BILL TO CONTACT',
            'BILL TO EMAIL',
            'BILL TO ADDRESS',
            'CURRENCY NAME',
            'CURRENCY CODE',
            'SUBTOTAL EXCLUDING TAX',
            'TOTAL DISCOUNT AMOUNT',
            'TOTAL AFTER DISCOUNT',
            'TOTAL TAX AMOUNT',
            'SHIPPING CHARGE',
            'PACKING CHARGE',
            'TOTAL AMOUNT',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($order): array
    {
        $order = collect(new InvoiceResource($order));
        return [
            (isset($order['bill_to']))?$order['bill_to']:'',
            (isset($order['invoice_number']))?$order['invoice_number']:'',
            (isset($order['invoice_reference']))?$order['invoice_reference']:'',
            (isset($order['invoice_date']))?$order['invoice_date']:'',
            (isset($order['invoice_due_date']))?$order['invoice_due_date']:'',
            (isset($order['bill_to_code']))?$order['bill_to_code']:'',
            (isset($order['bill_to_name']))?$order['bill_to_name']:'',
            (isset($order['bill_to_contact']))?$order['bill_to_contact']:'',
            (isset($order['bill_to_email']))?$order['bill_to_email']:'',
            (isset($order['bill_to_address']))?$order['bill_to_address']:'',
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
