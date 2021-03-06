<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserFormRequest;
use App\Models\Location;
use App\Models\Role;
use App\Models\User;
use App\Traits\Uploadable;
use Illuminate\Http\Request;

class CrudIndexController extends Controller
{
    use Uploadable;
     
    public function index(){

        $roles = Role::get();

        $districts = Location::where('parent_id', 0)->orderBy('location_name','asc')->get();

        return view('ajax-crud',compact('roles', 'districts'));
    }

    public function store(UserFormRequest $request){

        $data = $request->validated();

        $collection = collect($data)->except(['avatar','password_confirmation']);
        
        if(request()->file('avatar')){

            $user_avatar_name = str_replace(' ', '', request()->name).'_'.uniqid();

            $avatar = $this->upload_file(request()->file('avatar'),'User',$user_avatar_name);
            
            $collection = $collection->merge(compact('avatar'));
            if(!empty($request->old_avatar)){
                $this->delete_file($request->old_avatar,'User');
            }
        }

        $result = User::updateOrCreate(['id' => $request->update_id],$collection->all());

        if ($result) {
            $output = ['status' => 'success', 'message' => 'data has been saved successfully'];
        }else{
            if(!empty($avatar)){
                $this->delete_file($avatar,'User');
            }
            
            $output = ['status' => 'error', 'message' => 'data can not save'];
        }
        return response()->json($output);
        
    }

    public function userList(Request $request){
        if($request->ajax()){
            $user = new User();

            if(!empty($request->name)){
                $user->setName($request->name);
            }
            
            if(!empty($request->email)){
                $user->setEmail($request->email);
            }

            if(!empty($request->mobile_no)){
                $user->setMobileNo($request->mobile_no);
            }

            if(!empty($request->district_id)){
                $user->setDistrictId($request->district_id);
            }
            
            if(!empty($request->upazila_id)){
                $user->setUpazilaId($request->upazila_id);
            }
            
            if(!empty($request->role_id)){
                $user->setRoleId($request->role_id);
            }
            
            if(!empty($request->status)){
                $user->setStatus($request->status);
            }


            $user->setOrderValue($request->input('order.0.column'));
            $user->setDirValue($request->input('order.0.dir'));
            $user->setLengthValue($request->input('length'));
            $user->setStartValue($request->input('start'));

            $list = $user->getList();

            $data = [];
            $no   = $request->input('start');
            foreach ($list as $value) {
                $no++;
                $action ='';
                $action .='<a class="dropdown-item data_edit" data-id="'.$value->id.'"><i class="fa-solid fa-pen-to-square"></i> Edit</a>';
                $action .='<a class="dropdown-item data_view" data-id="'.$value->id.'"><i class="fa-solid fa-eye"></i> View</a>';
                $action .='<a class="dropdown-item data_delete" data-id="'.$value->id.'" data-name="'.$value->name.'"><i class="fa-solid fa-trash"></i> Delete</a>';
                $btnGroup = '<div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa-solid fa-list-ul"></i>
                </button>
                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                '.$action.'
                </div>
                </div>';

              $row = [];
              $row[] = $no;
              $row[] = $value->name;
              $row[] = $this->avatar($value->avatar,$value->name);
              $row[] = $value->role->role_name;
              $row[] = $value->email;
              $row[] = $value->mobile_no;
              $row[] = $value->district->location_name;
              $row[] = $value->upazila->location_name;
              $row[] = $value->postal_code;
              $row[] = $value->email_verified_at ? '<span class="badge bg-success">Verified</span>' : '<span class="badge bg-danger">Unverified</span>';
              $row[] = $this->changeStatus($value->status,$value->id);
              $row[] = $btnGroup;

              $data[] = $row;
            }
            $output = array(
                "draw"=>request()->input('draw'),
                "recordsTotal"=>$user->count_all(),
                "recordsFiltered"=>$user->count_filtered(),
                "data"=>$data
            );

            echo json_encode($output);
        }
    }

    private function changeStatus($status,$id){

        $checked = $status == 1 ? 'checked' : '';

        return  '<label class="switch">
                <input type="checkbox" class="change_status"  data-id="'.$id.'" '.$checked.'>
                <span class="slider round"></span>
                </label>';
    }

    private function avatar($avatar=null,$name){
        return !empty($avatar) ? '<img src="'.asset("storage/User/".$avatar).'" alt="'.$name.'" style="width:60px" />' : '<p>no image found</p>';
    }

    public function userEdit(Request $request){
        if($request->ajax()){
            $user = User::find($request->id);
            return response()->json($user);
        }
    }
    
    public function userChnageStatus(Request $request){
        if($request->ajax()){
            
            if ($request->id && $request->status) {
                $result = User::find($request->id)->update(['status'=>$request->status]);
                if($result){
                    $output = ['status' => 'success', 'message' => 'User status updated successfully'];
                }else{
                    $output = ['status' => 'error', 'message' => 'User status can\'t be updated'];
                }
            }
            return response()->json($output);
        }
    }

    public function userDestroy(Request $request){
        if($request->ajax()){
            $user = User::with('role','district','upazila')->find($request->id);
            if ($user) {
                $avatar = $user->avatar;
                if($user->delete()){
                    if(!empty($avatar)){
                        $this->delete_file($avatar,'User');
                    }
                    $output = ['status' => 'success', 'message' => 'data has been deleted successfully'];
                }else{
                    $output = ['status' => 'error', 'message' => 'data can\'t be deleted'];
                }
            }
            return response()->json($output);
        }
    }

    public function userShow(Request $request){
        if($request->ajax()){
            $user = User::find($request->id);
            if ($user) {
                $output['user_view'] = view('user-view',compact('user'))->render();
                $output['name'] = $user->name;
            }
            return response()->json($output);
        }
    }

    public function upazila_lsit(Request $request){
        if ($request->ajax()) {
            if($request->district_id){
                $output = '<option value="">Select Please</option>';
                $upazilas = Location::where('parent_id', $request->district_id)->orderBy('location_name','asc')->get();
                if($upazilas){
                    foreach ($upazilas as $upazila) {
                        $output .= '<option value="'.$upazila->id.'">'.$upazila->location_name.'</option>';
                    }
                }
                return response()->json($output);
            }
        }
    }
}
