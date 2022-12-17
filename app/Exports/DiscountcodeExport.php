<?php

namespace App\Exports;

use App\Models\Discountcode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\DiscountcodeResource;

use Carbon\Carbon;

class DiscountcodeExport implements FromCollection, WithMapping, WithHeadings
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

        $query = Discountcode::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('discount_codes.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('discount_codes.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('discount_codes.status', $status);
        }

        $discount_codes = $query->get();

        return $discount_codes;
    }

    public function headings(): array
    {
        return [
            'DISCOUNT CODE',
            'NAME',
            'DISCOUNT PERCENTAGE',
            'DESCRIPTION',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($discount_code): array
    {
        $discount_code = collect(new DiscountcodeResource($discount_code));
        return [
            (isset($discount_code['discount_code']))?$discount_code['discount_code']:'',
            (isset($discount_code['label']))?$discount_code['label']:'',
            (isset($discount_code['discount_percentage']))?$discount_code['discount_percentage']:'',
            (isset($discount_code['description']))?$discount_code['description']:'',            
            (isset($discount_code['status']['label']))?$discount_code['status']['label']:'',
            (isset($discount_code['created_at_label']))?$discount_code['created_at_label']:'',
            (isset($discount_code['created_by']['fullname']))?$discount_code['created_by']['fullname']:'',
            (isset($discount_code['updated_at_label']))?$discount_code['updated_at_label']:'',
            (isset($discount_code['updated_by']['fullname']))?$discount_code['updated_by']['fullname']:''
        ];
    }
}
