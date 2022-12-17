<?php

namespace App\Exports;

use App\Models\Taxcode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\TaxcodeResource;

use Carbon\Carbon;

class TaxcodeExport implements FromCollection, WithMapping, WithHeadings
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

        $query = Taxcode::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('tax_codes.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('tax_codes.created_at', '<=', $to_created_date);
        }
        if(isset($status)){
            $query = $query->where('tax_codes.status', $status);
        }

        $tax_codes = $query->get();

        return $tax_codes;
    }

    public function headings(): array
    {
        return [
            'TAX CODE',
            'NAME',
            'TAX PERCENTAGE',
            'DESCRIPTION',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($tax_code): array
    {
        $tax_code = collect(new TaxcodeResource($tax_code));
        return [
            (isset($tax_code['tax_code']))?$tax_code['tax_code']:'',
            (isset($tax_code['label']))?$tax_code['label']:'',
            (isset($tax_code['total_tax_percentage']))?$tax_code['total_tax_percentage']:'',
            (isset($tax_code['description']))?$tax_code['description']:'',            
            (isset($tax_code['status']['label']))?$tax_code['status']['label']:'',
            (isset($tax_code['created_at_label']))?$tax_code['created_at_label']:'',
            (isset($tax_code['created_by']['fullname']))?$tax_code['created_by']['fullname']:'',
            (isset($tax_code['updated_at_label']))?$tax_code['updated_at_label']:'',
            (isset($tax_code['updated_by']['fullname']))?$tax_code['updated_by']['fullname']:''
        ];
    }
}
