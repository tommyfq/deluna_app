<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class UserController extends Controller {
    
    private $page = 'user';
    public function __construct(){
	}

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords($this->page).' Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index')];
        
        $viewtarget = "pages.".$this->page.".index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0 =>'name',
                1 =>'email',
                2 =>'is_active',
                3 =>'id',
            );

            $totalData = User::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  User::where(function($query){});
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('email', 'LIKE',"%{$search}%");
                });

                $totalFiltered = User::where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('email', 'LIKE',"%{$search}%");
                    })
                    ->count();
            }

            $models = $models->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            $data = $models->toArray();

            if(!empty($data)){
                for($i = 0; $i < count($data); $i++){
                    $data[$i]['is_active'] = $data[$i]['is_active'] == true ? 
                    '<i class="fa fa-check text-success">'
                    :
                    '<i class="fa fa-close text-danger">';
                    $data[$i]['action'] = '
                    <a href="'.route('user.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    <a href="'.route('user.delete',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Close">
                        <i class="fa fa-close color-danger"></i>
                    </a>
                    ';
                }
            }
            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            );

            return json_encode($json_data);
        }
    }

    public function add() {
        $param = array();
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        
        $viewtarget = "pages.".$this->page.".add";
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
            'confirm_password' => 'required|same:password',
            'is_active' => 'required'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
            'same' => 'The :attribute and :other must match.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
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
        $user->is_active = $request->is_active === 'true' ? true: false;
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
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];

        $user = User::where(['id' => $slug])->first();
        if(!$user){
            Session::flash('message.error', "Data not found!");
            return redirect()->route('user.index');
        }
        $param['data'] = $user;
        $viewtarget = "pages.".$this->page.".edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function update(Request $request, $slug){
        // validation
        $rules = [
            'name' => 'required',
            'email' => 'required',
            'is_active' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
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
            'is_active' => $request->is_active === 'true' ? true: false,
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
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        User::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($user->delete()){
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route('user.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }
}
