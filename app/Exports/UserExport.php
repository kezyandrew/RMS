<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\Exportable;

use App\Http\Resources\UserResource;

use Carbon\Carbon;

class UserExport implements FromCollection, WithMapping, WithHeadings
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
        $role = $this->data['role'];
        $status = $this->data['status'];
        

        $query = User::query()
        ->select('users.*', 'roles.id')
        ->roleJoin()
        ->hideSuperAdminRole();

        if($from_created_date != ''){
            $from_created_date = strtotime($from_created_date);
            $from_created_date = date(config('app.sql_date_format'), $from_created_date);
            $from_created_date = $from_created_date . ' 00:00:00';
            $query = $query->where('users.created_at', '>=', $from_created_date);
        }
        if($to_created_date != ''){
            $to_created_date = strtotime($to_created_date);
            $to_created_date = date(config('app.sql_date_format'), $to_created_date);
            $to_created_date = $to_created_date . ' 23:59:59';
            $query = $query->where('users.created_at', '<=', $to_created_date);
        }
        if($role != ''){
            $query = $query->where('roles.slack', $role);
        }
        if(isset($status)){
            $query = $query->where('users.status', $status);
        }

        $users = $query->get();

        return $users;
    }

    public function headings(): array
    {
        return [
            'USER CODE',
            'FULL NAME',
            'EMAIL',
            'PHONE',
            'ROLE',
            'STATUS',
            'CREATED AT',
            'CREATED BY',
            'UPDATED AT',
            'UPDATED BY'
        ];
    }

    public function map($user): array
    {
        $user = collect(new UserResource($user));
        return [
            (isset($user['user_code']))?$user['user_code']:'',
            (isset($user['fullname']))?$user['fullname']:'',
            (isset($user['email']))?$user['email']:'',
            (isset($user['phone']))?$user['phone']:'',
            (isset($user['role']['label']))?$user['role']['label']:'',
            (isset($user['status']['label']))?$user['status']['label']:'',
            (isset($user['created_at_label']))?$user['created_at_label']:'',
            (isset($user['created_by']['fullname']))?$user['created_by']['fullname']:'',
            (isset($user['updated_at_label']))?$user['updated_at_label']:'',
            (isset($user['updated_by']['fullname']))?$user['updated_by']['fullname']:''
        ];
    }
}
