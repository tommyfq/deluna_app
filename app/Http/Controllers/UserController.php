<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller {

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Users Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'User' => route('user.index')];
        
        $viewtarget = "pages.user.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function add() {
        $param = array();
        $param['_title'] = 'Deluna | Add User';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'User' => route('user.index'), 'Add' => route('user.add')];
        
        $viewtarget = "pages.user.add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request){
        // validation
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'confirm_password' => 'required|same:password'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
            'same' => 'The :attribute and :other must match.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if user exist
        $check = User::where('email',$request->email)->first();
        if($check){
            Session::flash('message.error', 'Account already exists!');
            return redirect()->back();
        }
        // create new user
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = md5($request->password);
        if($user->save()){
            Session::flash('message.success', 'Account has been created!');
            return redirect()->route('user.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

}
