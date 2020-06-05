<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class SystemController extends Controller
{
    public function listUse(){
        $user_id_sign_in = Auth::id();
        $list_use = DB::table('manager_user')->get();
        $role_use = DB::table('manager_user_authority')
            ->select('authority_name')
            ->where('user_id','=',$user_id_sign_in)
            ->get();

        if(sizeof($role_use) == 2){
            $role_use_number = 1;
        } else if(sizeof($role_use) == 1) {
            foreach ($role_use as $value){
                if($value->authority_name == 'ROLE_ADMIN'){
                    $role_use_number = 1;
                }
                else{
                    $role_use_number = 0;
                }
            }
        }else{
            $role_use_number = 0;
        }
        return view('System.system_list')->with(['list_use'=>$list_use,'role_use_number'=>$role_use_number]);
    }

    public function updateUseView($id){
        $data = DB::table('manager_user')
            ->find($id);
        return view('System.infor_use')->with('data',$data);
    }

    public function updateUseViewInfor(Request  $request){
        $id_auth = Auth::id();
        $user_update = DB::table('manager_user')
            ->find($id_auth);
        $data = DB::table('manager_user')
            ->find($request->user_id);
        if(!Hash::check($request->txtPass,$data->password)) {
            $password = Hash::make($request->txtPass);
        }else{
            $password = $request->txtPass;
        }
        $dataupdate = DB::table('manager_user')->where('id','=',$request->user_id)
                    ->update([
                        'login'=>$request->txtNumber,
                        'password'=>$password,
                        'first_name'=>$request->txtFName,
                        'last_name'=>$request->txtLName,
                        'email'=>$request->txtEmail,
                        'image_url'=>$data->image_url,
                        "activated"=>$data->activated,
                        "lang_key"=>$data->lang_key,
                        "activation_key"=>$data->activation_key,
                        "reset_key"=>$data->reset_key,
                        "created_by"=>$data->created_by,
                        "created_date"=>$data->created_date,
                        "reset_date"=>$data->reset_date,
                        "last_modified_by"=>$user_update->login,
                        "last_modified_date"=>$data->last_modified_date]);
        if($dataupdate = 1){
            $notification = array(
                'message' => 'Cập nhật thông tin thành công!',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Cập nhật thông tin không thành công!',
                'alert-type' => 'success'
            );
        }
        return Redirect::back()->with($notification);
    }

    public function registerProcess(Request $request){
        $validator = Validator::make($request->all(), [
            'txtName' => 'required|max:50',
            'txtEmail' => 'required|max:191|email',
            'txtPassword' => 'required|max:60',
            'txtFName' => 'required|max:50',
            'txtLName'=>'required|max:50'
        ]);
        $noti= array(
            'message' => ' Đăng ký lỗi! Hãy chọn phần tạo tài khoản và nhập lại thông tin!',
            'alert-type' => 'error'
        );
        if ($validator->fails()) {
            return Redirect::back()
                ->with($noti)
                ->withErrors($validator)
                ->withInput();
        }
        $id_auth = Auth::id();
        $user_create = DB::table('manager_user')
            ->find($id_auth);
        try {
            $create_db = DB::table('manager_user')->insert([
                'login' => $request['txtName'],
                'password' => Hash::make($request['txtPassword']),
                'first_name' => $request['txtFName'],
                'last_name' => $request['txtLName'],
                'email' => $request['txtEmail'],
                'activated' => 1,
                'lang_key' => 'vi',
                'created_by' => $user_create->login,
            ]);
        } catch (QueryException $ex){
            $notification = array(
                'message' => 'Email đã có trong danh sách! Vui lòng nhập email khác ',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
        $notification = array(
            'message' => 'Cập nhật thông tin thành công!',
            'alert-type' => 'success'
        );
        return Redirect::back()->with($notification);

    }

    public function deleteUseView($id){
        $data = DB::table('manager_user')
                ->delete($id);
        if($data = 1){
            $notification = array(
                'message' => 'Xoá thông tin thành công!',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Xóa thông tin không thành công!',
                'alert-type' => 'success'
            );
        }
        return Redirect::back()->with($notification);
    }

    public function viewRoleUse(){
        $data = DB::table('manager_user')
                ->join('manager_user_authority','manager_user_authority.user_id','=','manager_user.id')
                ->select('manager_user.id','manager_user.login','manager_user.email','manager_user_authority.authority_name')
                ->get();
        $acc_has_role = DB::table('manager_user_authority')->get();
        foreach ($acc_has_role as $user){
            $data_acc_role[] = $user->user_id;
        }
        $acc_not_role = DB::table('manager_user')
                        ->whereNotIn('id',$data_acc_role)
                        ->get();
        $user_id_sign_in = Auth::id();
        $role_use = DB::table('manager_user_authority')
            ->select('authority_name')
            ->where('user_id','=',$user_id_sign_in)
            ->get();

        if(sizeof($role_use) == 2){
            $role_use_number = 1;
        } else if(sizeof($role_use) == 1) {
            foreach ($role_use as $value){
                if($value->authority_name == 'ROLE_ADMIN'){
                    $role_use_number = 1;
                }
                else{
                    $role_use_number = 0;
                }
            }
        }else{
            $role_use_number = 0;
        }
        return view('System.role_use_list')->with(['data'=>$data,'role_use_number'=>$role_use_number,'acc_not_role'=>$acc_not_role]);
    }

    public function updateRoleView($id){
        $data = DB::table('manager_user_authority')
                ->where('user_id','=',$id)
                ->get();
        $role_admin = 0;
        $role_user = 0;
        if (sizeof($data)== 2){
            $role_admin = 1;
            $role_user = 1;
        }elseif (sizeof($data) == 1){
            foreach ($data as $value){
                if($value->authority_name == 'ROLE_ADMIN'){
                    $role_admin = 1;
                }
                else{
                    $role_user = 1;
                }
            }
        }
        return view('System.add_role')->with(['role_admin'=>$role_admin,'role_user'=>$role_user, 'id'=>$id]);
    }

    public function updateRoleViewInfor(Request $request){

        $data = DB::table('manager_user')
            ->Join('manager_user_authority','manager_user_authority.user_id','=','manager_user.id')
            ->select('manager_user.id','manager_user.login','manager_user.email','manager_user_authority.authority_name')
            ->where('id','=',$request['user_id'])
            ->get();

        if(sizeof($data) == 2 && ($request['roleAdmin'] == null || $request['roleUser'] == null) ){
            if ($request['roleAdmin'] == null && $request['roleUser'] != null){
                DB::table('manager_user_authority')
                    ->where('user_id','=',$request['user_id'])
                    ->where('authority_name','=','ROLE_ADMIN')
                    ->delete();
            }
            else if ($request['roleUser'] == null && $request['roleAdmin'] != null ){
                DB::table('manager_user_authority')
                    ->where('user_id','=',$request['user_id'])
                    ->where('authority_name','=','ROLE_USER')
                    ->delete();
            }else{
                DB::table('manager_user_authority')
                    ->where('user_id','=',$request['user_id'])
                    ->where('authority_name','=','ROLE_ADMIN')
                    ->delete();
                DB::table('manager_user_authority')
                    ->where('user_id','=',$request['user_id'])
                    ->where('authority_name','=','ROLE_USER')
                    ->delete();
            }
        }

        if(sizeof($data) == 1 && $request['roleAdmin'] != null && $request['roleUser'] != null ){
            DB::table('manager_user_authority')->where('user_id','=',$request['user_id'])
                ->update(['authority_name'=>$request['roleUser']]);
            DB::table('manager_user_authority')->insert([
                'user_id' => $request['user_id'],
                'authority_name'=>$request['roleAdmin'],
            ]);
        }

        if(sizeof($data) == 1 && ($request['roleAdmin'] == null || $request['roleUser'] == null) ){
            if ($request['roleAdmin'] == null && $request['roleUser'] != null){
                DB::table('manager_user_authority')->where('user_id','=',$request['user_id'])
                    ->update(['authority_name'=>$request['roleUser']]);
            }
            else if ($request['roleUser'] == null && $request['roleAdmin'] != null ){
                DB::table('manager_user_authority')->where('user_id','=',$request['user_id'])
                    ->update(['authority_name'=>$request['roleAdmin']]);
            }else{
                DB::table('manager_user_authority')->where('user_id','=',$request['user_id'])->delete();
            }
        }

        $notification = array(
            'message' => 'Cập nhật thông tin thành công!',
            'alert-type' => 'success'
        );
        return Redirect::back()->with($notification);

    }

    public function addRoleView($id){
        $data = DB::table('manager_user')->find($id);
        return view('System.add_role_new')->with('data',$data);
    }

    public function addRoleViewInfor(Request $request){
        if($request['roleAdmin'] != null && $request['roleUser'] != null){
            DB::table('manager_user_authority')->insert([
                'user_id' => $request['user_id'],
                'authority_name'=>$request['roleAdmin'],
            ]);
            DB::table('manager_user_authority')->insert([
                'user_id' => $request['user_id'],
                'authority_name'=>$request['roleUser'],
            ]);
        }else{
            if($request['roleAdmin'] != null && $request['roleUser'] == null){
                DB::table('manager_user_authority')->insert([
                    'user_id' => $request['user_id'],
                    'authority_name'=>$request['roleAdmin'],
                ]);
            }elseif ($request['roleAdmin'] == null && $request['roleUser'] != null){
                DB::table('manager_user_authority')->insert([
                    'user_id' => $request['user_id'],
                    'authority_name'=>$request['roleUser'],
                ]);
            }
        }
        $notification = array(
            'message' => 'Thêm thông tin thành công!',
            'alert-type' => 'success'
        );
        return Redirect::back()->with($notification);

    }

    public  function deleteRoleFromAdmin($id,$role){
        $delete_role = DB::table('manager_user_authority')
                        ->where('user_id','=',$id)
                        ->where('authority_name','=',$role)
                        ->delete();
        if($data = 1){
            $notification = array(
                'message' => 'Xoá thông tin thành công!',
                'alert-type' => 'success'
            );
        }else{
            $notification = array(
                'message' => 'Xóa thông tin không thành công!',
                'alert-type' => 'success'
            );
        }
        return Redirect::back()->with($notification);
    }



}
