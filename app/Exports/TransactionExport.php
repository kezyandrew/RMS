<?php

namespace App\Exports;

use App\Models\Transaction;
use App\Models\MasterTransactionType as MasterTransactionTypeModel;
use App\Models\PaymentMethod as PaymentMethodModel;
use App\Models\Account as AccountModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\TransactionResource;

use Carbon\Carbon;

class TransactionExport implements FromCollection, WithMapping, WithHeadings
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
        $account = $this->data['account'];
        $transaction_type = $this->data['transaction_type'];
        $payment_method = $this->data['payment_method'];
        $bill_to = $this->data['bill_to'];

        $query = Transaction::query();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('transactions.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('transactions.created_at', '<=', $to_created_date);
        }
        if(isset($account)){
            $account_data = AccountModel::select('id')
            ->where('slack', '=', trim($account))
            ->first();

            $query = $query->where('transactions.account_id', $account_data->id);
        }

        if(isset($transaction_type)){
            $transaction_type_data = MasterTransactionTypeModel::select('id')
            ->where('transaction_type_constant', '=', trim($transaction_type))
            ->first();

            $query = $query->where('transactions.transaction_type', $transaction_type_data->id);
        }

        if(isset($payment_method)){
            
            $payment_method_data = PaymentMethodModel::select('id', 'label')
            ->where('slack', '=', trim($payment_method))
            ->first();

            $query = $query->where('transactions.payment_method_id', $payment_method_data->id);
        }

        if(isset($bill_to)){
            $query = $query->where('transactions.bill_to', $bill_to);
        }

        $transactions = $query->get();

        return $transactions;
    }

    public function headings(): array
    {
        return [
            'TRANSACTION DATE',
            'TRANSACTION CODE',
            'ACCOUNT CODE',
            'ACCOUNT NAME',
            'TRANSACTION TYPE',
            'PAYMENT METHOD',
            'PAYMENT FOR',
            'BILL TO NAME',
            'BILL TO CONTACT',
            'BILL TO ADDRESS',
            'CURRENCY CODE',
            'TOTAL AMOUNT',
            'PAYMENT GATEWAY TRANSACTION ID',
            'PAYMENT GATEWAY TRANSACTION STATUS',
            'CREATED AT',
            'CREATED BY',
        ];
    }

    public function map($transaction): array
    {
        $transaction = collect(new TransactionResource($transaction));
        return [
            (isset($transaction['transaction_date']))?$transaction['transaction_date']:'',
            (isset($transaction['transaction_code']))?$transaction['transaction_code']:'',
            (isset($transaction['account']['account_code']))?$transaction['account']['account_code']:'',
            (isset($transaction['account']['label']))?$transaction['account']['label']:'',
            (isset($transaction['transaction_type_data']['label']))?$transaction['transaction_type_data']['label']:'',
            (isset($transaction['payment_method']))?$transaction['payment_method']:'',
            (isset($transaction['bill_to']))?$transaction['bill_to']:'',
            (isset($transaction['bill_to_name']))?$transaction['bill_to_name']:'',
            (isset($transaction['bill_to_contact']))?$transaction['bill_to_contact']:'',
            (isset($transaction['bill_to_address']))?$transaction['bill_to_address']:'',
            (isset($transaction['currency_code']))?$transaction['currency_code']:'',
            (isset($transaction['amount']))?$transaction['amount']:'',
            (isset($transaction['pg_transaction_id']))?$transaction['pg_transaction_id']:'',
            (isset($transaction['pg_transaction_status']))?$transaction['pg_transaction_status']:'',
            (isset($transaction['created_at_label']))?$transaction['created_at_label']:'',
            (isset($transaction['created_by']['fullname']))?$transaction['created_by']['fullname']:''
        ];
    }
}
