<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsersExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    use Exportable;

    protected  $column_order = ['id','name','','role_id','email','mobile_no','district_id',
    'upazila_id','postal_code','email_verified_at','status',''];

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $query = User::with(['role','district','upazila']);

        if(!empty($this->data['column']) && !empty($this->data['dir'])){
            $query->orderBy($this->column_order[$this->data['column']],$this->data['dir']);
        }else{
            $query->orderBy('id','desc');
        }

        if($this->data['length'] != -1){
            $query->offset($this->data['start'])->limit($this->data['length']);
        }

        $list = $query->get();
        
        $data = [];

        foreach ($list as $value) {
            $row = [];

            $row['name']        = $value->name;
            $row['role']        = $value->role->role_name;
            $row['email']       = $value->email;
            $row['mobile']      = $value->mobile_no;
            $row['distruct']    = $value->district->location_name;
            $row['upazila']     = $value->upazila->location_name;
            $row['postal_code'] = $value->postal_code;
            $row['address']     = $value->address;
            $row['status']      = $value->status == 1 ? 'Active' : 'Inactive';

            $data[] = $row;
        }

        return collect($data);

    }

    public function headings(): array
    {
        return [
            'Name','Role','Email','Mobile','District','Upazila','Postal_code','Address','Status'
        ];
    }
    
}
