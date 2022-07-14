<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

use App\Models\User;

class AuthController extends Controller {

    public function __construct(){
	}

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Login';
        
        // helpers
        if(is_member()){
            Session::flash('message.warning', "You already login!");
            return redirect()->route('dashboard.index');
        }
        
        $viewtarget = "pages.login.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.login', $param);
    }

    public function doLogin(Request $request){
        // validation
        $rules = [
            'email' => 'required',
            'password' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check user
        $check = User::where('email', $request->email)->first();
        if(!$check){
            if($check->password != md5($password)){
                Session::flash('message.error', "Wrong username or password");
                return redirect()->back();
            }
            Session::flash('message.error', "Account doesn't exists!");
            return redirect()->back();
        }
        Session::put('user', $check);
        Session::flash('message.success', "Welcome, ".$check->name."!");
        return redirect()->route('dashboard.index');
    }

    public function doLogout(Request $request){
        Session::flush();
        Session::flash('message.success', "Your session has been logout!");
        return redirect()->route('login.index');
    }

}
