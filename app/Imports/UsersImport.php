<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UsersImport implements ToCollection,WithHeadingRow
{
    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows as $row) 
        {
                $data[] = [
                    'role_id'     => $row['role'],
                    'name'        => $row['name'],
                    'email'       => $row['email'],
                    'mobile_no'   => $row['mobile_no'],
                    'district_id' => $row['district'],
                    'upazila_id'  => $row['upazila'],
                    'postal_code' => $row['postal_code'],
                    'address'     => $row['address'],
                    'password'    => $row['password'],
                    'status'      => $row['status'],
                ];
        }

        User::insert($data);
    }
}
