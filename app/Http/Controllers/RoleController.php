<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Role;
use App\Models\RoleMapping;
use App\Models\Menu;
use App\Models\MenuAction;
use DB;

class RoleController extends Controller {

    private $page = 'role';
    public function __construct(){
	}

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords($this->page).' Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index')];
        $param['_page'] = $this->page;
        
        $viewtarget = "pages.".$this->page.".index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0 =>'role_name',
                1 =>'is_active',
                2 =>'id',
            );

            $totalData = Role::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Role::where(function($query){});
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('role_name','LIKE',"%{$search}%");
                });

                $totalFiltered = Role::where(function($query) use ($search){
                    $query->where('role_name','LIKE',"%{$search}%");
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
                    <a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    <a href="'.route($this->page.'.delete',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Close">
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
        $param = $arr = $temp = array();
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['_page'] = $this->page;
        $list_menu = Menu::get();
        foreach($list_menu as $val){
            $temp['menu_id'] = $val->id;
            $temp['menu_name'] = $val->menu;
            $temp['action'] = MenuAction::where('menu_id', $val->id)->get(['id as action_id', 'action_name']);
            array_push($arr, (object)$temp);
        }
        $param['_menu'] = (object)$arr;
        
        $viewtarget = "pages.".$this->page.".add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request){
        // validation
        $rules = [
            'name' => 'required',
            'menu' => 'required'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if role exist
        $check = Role::where(['role_name' => $request->name, 'deleted_at' => NULL])->first();
        if($check){
            Session::flash('message.error', ucwords($this->page).' already exists!');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            // create new role
            $role = new Role;
            $role->role_name = $request->name;
            $role->created_by = Session::get('user')->id;
            $role->is_active = $request->is_active === 'true' ? true: false;
            $role->save();
            // insert mapping role
            foreach($request->menu as $key => $val){
                foreach($val as $cval){
                    $arr = [
                        'role_id' => $role->id,
                        'menu_id' => $key,
                        'action_id' => $cval
                    ];
                    $role_mapping = RoleMapping::firstOrNew($arr);
                    $role_mapping->created_by = Session::get('user')->id;
                    $role_mapping->save();
                }
            }
            DB::commit();
            Session::flash('message.success', ucwords($this->page).' has been created!');
            return redirect()->route($this->page.'.index');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back()->withInput();
        }
    }

    public function edit($slug) {
        $param = $arr = $temp = array();
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];

        $role = Role::find($slug);
        if(!$role){
            Session::flash('message.error', "Data not found!");
            return redirect()->route($this->page.'.index');
        }

        $list_menu = Menu::get();
        foreach($list_menu as $val){
            $temp['menu_id'] = $val->id;
            $temp['menu_name'] = $val->menu;
            $temp['action'] = MenuAction::where('menu_id', $val->id)->get(['id as action_id', 'action_name']);
            foreach($temp['action'] as $cval){
                $temps = RoleMapping::where(['role_id' => $slug, 'action_id' => $cval->action_id])->first();
                if($temps){
                    $cval->checked = true;
                } else {
                    $cval->checked = false;
                }
            }
            array_push($arr, (object)$temp);
        }
        $param['_menu'] = (object)$arr;
        // list checked
        $mapping = RoleMapping::where(['role_id' => $slug])->get();
        $param['_mapping'] = $mapping;
        $param['data'] = $role;
        $param['_page'] = $this->page;
        $viewtarget = "pages.".$this->page.".edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function update(Request $request, $slug){
        // validation
        $rules = [
            'name' => 'required',
            'menu' => 'required'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if role exist
        $role = Role::find($slug);
        if(!$role){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        // update category
        DB::beginTransaction();
        try {
            $array = [
                'role_name' => $request->name,
                'is_active' => $request->is_active === 'true' ? true: false,
                'updated_by' => Session::get('user')->id,
            ];
            $role = Role::where('id', $slug)->update($array);
            foreach($request->menu as $key => $val){
                foreach($val as $cval){
                    $arr = [
                        'role_id' => $slug,
                        'menu_id' => $key,
                        'action_id' => $cval
                    ];
                    $role_mapping = RoleMapping::firstOrNew($arr);
                    $role_mapping->created_by = Session::get('user')->id;
                    $role_mapping->save();
                }
            }
            DB::commit();
            Session::flash('message.success', ucwords($this->page).' has been updated!');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function delete($slug){
        if(!$slug){
            Session::flash('message.error', 'No data selected!');
            return redirect()->back();
        }
        // check if category exist
        $role = Role::find($slug);
        if(!$role){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        Role::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($role->delete()){
            // delete all stocks
            RoleMapping::where('role_id', $slug)->update(['deleted_by' => Session::get('user')->id]);
            RoleMapping::where('role_id', $slug)->delete();
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }
 }
