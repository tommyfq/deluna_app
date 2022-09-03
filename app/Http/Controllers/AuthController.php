<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use DB;

use App\Models\User;
use App\Models\RoleMapping;
use App\Models\Menu;
use App\Models\Role;

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
        $check = User::select('ms_users.id','ms_users.name','ms_users.role_id','ms_users.email','r.role_name','ms_users.is_active','ms_users.password')->leftJoin(with(new Role)->getTable().' as r', function($join){
                        $join->on('r.id', with(new User)->getTable().'.role_id');
                    })
                    ->where('email', $request->email)->first();
        if(!$check){
            Session::flash('message.error', "Account doesn't exists!");
            return redirect()->back();
        }
        if($check->password != md5($request->password)){
            Session::flash('message.error', "Wrong username or password");
            return redirect()->back();
        }
        // get menu parent
        
        $menu = Menu::whereNull('parent_id')
                    ->orderBy('menu', 'ASC')
                    ->get(['id', 'menu', 'slug', 'icon', 'view', 'add', 'edit', 'delete', 'print']);
        // get menu child
        $arr = $temp = array();
        foreach($menu as $val){
            $temp['id'] = $val->id;
            $temp['menu'] = $val->menu;
            $temp['slug'] = $val->slug;
            $temp['icon'] = $val->icon;
            $temp['view'] = $val->view;
            $temp['add'] = $val->add;
            $temp['edit'] = $val->edit;
            $temp['delete'] = $val->delete;
            $temp['print'] = $val->print;
            $temp['submenu'] = RoleMapping::leftJoin(with(new Menu)->getTable(). ' as m', function($join){
                                                $join->on('m.id', with(new RoleMapping)->getTable().'.menu_id');
                                            })
                                            ->where('role_id', $check->role_id)
                                            ->where('parent_id', $val->id)
                                            ->where( with(new RoleMapping)->getTable().'.view', '!=', 0)
                                            ->orderBy('m.menu', 'ASC')
                                            ->get(['m.id', 'm.menu', 'm.slug', 'm.icon', with(new RoleMapping)->getTable().'.view', with(new RoleMapping)->getTable().'.add', with(new RoleMapping)->getTable().'.edit', with(new RoleMapping)->getTable().'.delete', with(new RoleMapping)->getTable().'.print']);
            array_push($arr, (object)$temp);
        }
        Session::put('user', $check);
        Session::put('menu', $arr);
        Session::flash('message.success', "Welcome, ".$check->name."!");
        return redirect()->route('dashboard.index');
    }

    public function doLogout(Request $request){
        Session::flush();
        Session::flash('message.success', "Your session has been logout!");
        return redirect()->route('login.index');
    }

}
