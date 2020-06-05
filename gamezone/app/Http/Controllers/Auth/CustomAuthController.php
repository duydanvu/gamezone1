<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
//use Validator;
//use Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;
use App\User;
use Illuminate\Support\Facades\Hash;

class CustomAuthController extends Controller
{

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            return Redirect::back()
                        ->withErrors($validator)
                        ->withInput();
        }
        $credentials = [
            'login' => $request['login'],
            'password' => $request['password'],
        ];
        $password = Hash::make($request['password']);
//        dd($password);
        $remember_me = $request->has('remember') ? true : false;
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            session ( [
                'login' => $request->get( 'login' ) ,
                'name' => $user->last_name,
                'email' => $user->email
            ] );
            $notification = array(
                'message' => 'Hello '.$user->last_name.'!',
                'alert-type' => 'success'
            );
            return redirect('/home')->with($notification);
        }else
        {
            $notification = array(
                'message' => 'Login fail! Please re-check your email and password',
                'alert-type' => 'error'
            );
            return Redirect::back()->with($notification);
        }
    }

    public function logout()
    {
        Session::flush ();
		Auth::logout ();
		return redirect('/');
    }

    public function test()
    {
        # code...
    }

}
