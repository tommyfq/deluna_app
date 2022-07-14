<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller {

    public function __construct(){
	}

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
        $user->created_by = Session::get('user')->id;
        if($user->save()){
            Session::flash('message.success', 'Account has been created!');
            return redirect()->route('user.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function edit($slug) {
        $param = array();
        $param['_title'] = 'Deluna | Edit User';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'User' => route('user.index'), 'Edit' => route('user.edit',[$slug])];

        $user = User::where('id',$slug)->first();
        if(!$user){
            Session::flash('message.error', "Data not found!");
            return redirect()->route('user.index');
        }
        $param['data'] = $user;
        $viewtarget = "pages.user.edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function update(Request $request, $slug){
        // validation
        $rules = [
            'name' => 'required',
            'email' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if user exist
        $user = User::find($slug);
        if(!$user){
            Session::flash('message.error', 'Account doesn\'t exists!');
            return redirect()->back();
        }
        // update user
        $array = [
            'name' => $request->name,
            'email' => $request->email,
            'updated_by' => Session::get('user')->id,
        ];
        if($request->password)
           $array['password'] = md5($request->password);
        $user = User::where('id', $slug)->update($array);
        if($user){
            Session::flash('message.success', 'Account has been updated!');
            return redirect()->route('user.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function delete($slug){
        if(!$slug){
            Session::flash('message.error', 'No data selected!');
            return redirect()->back();
        }
        // check if user exist
        $user = User::find($slug);
        if(!$user){
            Session::flash('message.error', 'Account doesn\'t exists!');
            return redirect()->back();
        }
        User::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($user->delete()){
            Session::flash('message.success', 'Account has been deleted!');
            return redirect()->route('user.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }
}
