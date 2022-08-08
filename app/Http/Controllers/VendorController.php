<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Vendor;
use DataTables;

class VendorController extends Controller {

    private $page = 'vendor';
    public $menu;
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->menu = $request->get('menu');
            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords($this->page).'s Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index')];
        $param['_page'] = $this->page;
        $param['_role'] = $request->get('role');
        $param['_sidebar'] = $this->menu;

        $viewtarget = "pages.".$this->page.".index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        
        return view('layouts.master', $param);
    }

    public function get(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $columns = array(
                0 =>'name',
                1 =>'slug',
                2 =>'address',
                3 =>'phone',
                4 =>'is_active',
            );

            $totalData = Vendor::count();

            $role = $request->get('role');
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Vendor::where(function($query){});
            if(!empty($request->input('search.value'))){
                $search = $request->input('search.value');
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('address', 'LIKE',"%{$search}%")
                        ->orWhere('phone', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Vendor::where(function($query) use ($search){
                        $query->where('name','LIKE',"%{$search}%")
                            ->orWhere('address', 'LIKE',"%{$search}%")
                            ->orWhere('phone', 'LIKE',"%{$search}%");
                    })->count();
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
                    $data[$i]['action'] = '';
                    if($role->edit)
                        $data[$i]['action'] .= '
                        <a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="fa fa-pencil color-muted m-r-5"></i> 
                        </a>';
                    if($role->delete)
                        $data[$i]['action'] .= '<a href="'.route($this->page.'.delete',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Close">
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

    public function add() 
    {
        $param = array();
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['_page'] = $this->page;
        $param['_sidebar'] = $this->menu;

        $viewtarget = "pages.".$this->page.".add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request)
    {
        // validation
        $rules = [
            'name' => 'required',
            'slug' => 'required',
            'address' => 'required',
            'phone' => 'required | regex:/^([0-9\s\-\+\(\)]*)$/ | min:10'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }
        
        $param = $request->all();
        $param['phone'] = '+62'.$param['phone'];
        unset($param['_token']);

        // check if vendor exist
        $check = Vendor::where('slug',$request->slug)->orWhere('name',$request->name)->first();
        if($check){
            Session::flash('message.error', ucwords($this->page).' already exists!');
            return redirect()->back()->withInput();
        }
        
        $param['is_active'] = $param['is_active'] === 'true' ? true: false;
        $param['created_by'] = Session::get('user')->id;
        $result = Vendor::create($param);
        
        if($result->wasRecentlyCreated === true){
            Session::flash('message.success', ucwords($this->page).' has been created!');
            return redirect()->route($this->page.'.index');
        }else{
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back()->withInput();
        }
    }

    public function edit($slug)
    {
        $param = array();
        $param['_title'] = 'Deluna | Edit User';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];
        $param['_page'] = $this->page;
        $param['_sidebar'] = $this->menu;

        $selected = Vendor::where('id', $slug)->first();
        if(!$selected){
            Session::flash('message.error', "Data not found!");
            return redirect()->route($this->page.'.index');
        }
        $selected->is_active = $selected->is_active == 1 ? 'true' : 'false';
        $selected->phone = str_replace('+62','',$selected->phone);
        $param['data'] = $selected;
        $viewtarget = "pages.".$this->page.".edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }
    
    public function update(Request $request, $slug)
    {
        // validation
        $rules = [
            'name' => 'required',
            'slug' => 'required',
            'address' => 'required',
            'phone' => 'required | regex:/^([0-9\s\-\+\(\)]*)$/ | min:10'
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if user exist
        $user = Vendor::find($slug);
        if(!$user){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        // update user
        $param = $request->all();
        $param['phone'] = '+62'.$param['phone'];
        unset($param['_token']);
        unset($param['_method']);
        $param['is_active'] = $param['is_active'] === 'true' ? true: false;
        $param['updated_by'] = Session::get('user')->id;
        $result = Vendor::where('id', $slug)->update($param);
        if($result){
            Session::flash('message.success', ucwords($this->page).' has been updated!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function delete($slug)
    {
        if(!$slug){
            Session::flash('message.error', 'No data selected!');
            return redirect()->back();
        }
        // check if user exist
        $data = Vendor::find($slug);
        if(!$data){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        Vendor::where('id',$slug)->update(['deleted_by' => Session::get('user')->id, 'is_active' => 0]);
        if($data->delete()){
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }

}
