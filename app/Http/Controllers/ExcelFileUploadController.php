<?php

namespace App\Http\Controllers;

use App\Imports\UsersImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExcelFileUploadController extends Controller
{
    public function index(Request $request){

        if($request->ajax()){
            if($request->file('excel')){
                if($request->type == 1){
                    $result = Excel::import(new UsersImport,$request->file('excel'));
                }else{
                    //
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
}
