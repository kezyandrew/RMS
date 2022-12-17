<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\CustomerResource;

use Carbon\Carbon;

class CustomerExport implements FromCollection, WithMapping, WithHeadings
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

        $query = Customer::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('customers.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('customers.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('customers.status', $status);
        }

        $customers = $query->get();

        return $customers;
    }

    public function headings(): array
    {
        return [
            'NAME',
            'EMAIL',
            'PHONE',
            'ADDRESS',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($customer): array
    {
        $customer = collect(new CustomerResource($customer));
        return [
            (isset($customer['name']))?$customer['name']:'',
            (isset($customer['email']))?$customer['email']:'',
            (isset($customer['phone']))?$customer['phone']:'',
            (isset($customer['address']))?$customer['address']:'',            
            (isset($customer['status']['label']))?$customer['status']['label']:'',
            (isset($customer['created_at_label']))?$customer['created_at_label']:'',
            (isset($customer['created_by']['fullname']))?$customer['created_by']['fullname']:'',
            (isset($customer['updated_at_label']))?$customer['updated_at_label']:'',
            (isset($customer['updated_by']['fullname']))?$customer['updated_by']['fullname']:''
        ];
    }
}
