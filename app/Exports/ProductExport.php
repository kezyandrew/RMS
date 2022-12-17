<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\ProductResource;

use Carbon\Carbon;

class ProductExport implements FromCollection, WithMapping, WithHeadings
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
        $supplier = $this->data['supplier'];
        $category = $this->data['category'];
        $tax_code = $this->data['tax_code'];
        $discount_code = $this->data['discount_code'];
        $product_type = $this->data['product_type'];
        $status = $this->data['status'];
        

        $query = Product::query()
        ->select('products.*', 'category.id', 'suppliers.id', 'tax_codes.id', 'discount_codes.id')
        ->categoryJoin()
        ->supplierJoin()
        ->taxcodeJoin()
        ->discountcodeJoin();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('products.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('products.created_at', '<=', $to_created_date);
        }
        if($supplier != ''){
            $query = $query->where('suppliers.slack', $supplier);
        }
        if($category != ''){
            $query = $query->where('category.slack', $category);
        }
        if($tax_code != ''){
            $query = $query->where('tax_codes.slack', $tax_code);
        }
        if($discount_code != ''){
            $query = $query->where('discount_codes.slack', $discount_code);
        }
        if(isset($status)){
            $query = $query->where('products.status', $status);
        }

        $query = $query->when($product_type == 'billing_products', function ($query) {
            $query->mainProduct();
        });

        $query = $query->when($product_type == 'ingredients', function ($query) {
            $query->isIngredient();
        });

        $products = $query->get();

        return $products;
    }

    public function headings(): array
    {
        return [
            'PRODUCT CODE',
            'NAME',
            'DESCRIPTION',
            'SUPPLIER CODE',
            'SUPPLIER NAME',
            'CATEGORY CODE',
            'CATEGORY NAME',
            'TAX CODE',
            'TAX PERCENTAGE',
            'DISCOUNT CODE',
            'DISCOUNT PERCENTAGE',
            'QUANTITY',
            'PURCHASE PRICE WITHOUT TAX',
            'SALE PRICE WITHOUT TAX',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($product): array
    {
        $product = collect(new ProductResource($product));
        return [
            (isset($product['product_code']))?$product['product_code']:'',
            (isset($product['name']))?$product['name']:'',
            (isset($product['description']))?$product['description']:'',
            
            (isset($product['supplier']['supplier_code']))?$product['supplier']['supplier_code']:'',
            (isset($product['supplier']['name']))?$product['supplier']['name']:'',

            (isset($product['category']['category_code']))?$product['category']['category_code']:'',
            (isset($product['category']['label']))?$product['category']['label']:'',

            (isset($product['tax_code']['tax_code']))?$product['tax_code']['tax_code']:'',
            (isset($product['tax_code']['total_tax_percentage']))?$product['tax_code']['total_tax_percentage']:'',

            (isset($product['discount_code']['discount_code']))?$product['discount_code']['discount_code']:'',
            (isset($product['discount_code']['discount_percentage']))?$product['discount_code']['discount_percentage']:'',

            (isset($product['quantity']))?$product['quantity']:'',
            (isset($product['purchase_amount_excluding_tax']))?$product['purchase_amount_excluding_tax']:'',
            (isset($product['sale_amount_excluding_tax']))?$product['sale_amount_excluding_tax']:'',

            (isset($product['status']['label']))?$product['status']['label']:'',
            (isset($product['created_at_label']))?$product['created_at_label']:'',
            (isset($product['created_by']['fullname']))?$product['created_by']['fullname']:'',
            (isset($product['updated_at_label']))?$product['updated_at_label']:'',
            (isset($product['updated_by']['fullname']))?$product['updated_by']['fullname']:'',
        ];
    }
}
