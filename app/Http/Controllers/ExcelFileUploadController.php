<?php

namespace App\Http\Controllers;

use App\Exports\UsersExport;
use App\Imports\UsersImport;
use Illuminate\Http\Request;
use App\Imports\MultipleSheetImport;
use Maatwebsite\Excel\Facades\Excel;


class ExcelFileUploadController extends Controller
{
    public function index(Request $request){

        if($request->ajax()){
            if($request->file('excel')){
                if($request->type == 1){
                    $result = Excel::import(new UsersImport,$request->file('excel'));
                }else{
                    $import = new MultipleSheetImport();
                    $import->onlySheets('users', 'roles');
                    $result = Excel::import($import, $request->file('excel'));
                }
                if($result){
                    $output = ['status' => 'success', 'message' => 'data has been saved successfully'];
                }else{
                    $output = ['error' => 'success', 'message' => 'data can\'t be saved'];
                }
            }else{
                $output = ['status' => 'error', 'message' => 'Please upload a excel file'];
            }
        }
        return response()->json($output);
    }

    public function export(Request $request){
        $data = [
            'start'  => $request->get('start'),
            'length' => $request->get('length'),
            'column' => $request->get('column'),
            'dir'    => $request->get('dir'),
        ];
        return Excel::download(new UsersExport($data),'users.xlsx');
    }
}
