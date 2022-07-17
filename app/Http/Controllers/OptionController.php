<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\OptionType;
use App\Models\Option;
use DataTables;
use Carbon\Carbon;

class OptionController extends Controller {

    private $page = 'option';
    public function __construct(){}

    public function index(Request $request)
    {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords($this->page).'s Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index')];
        $param['_page'] = $this->page;

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

            $totalData = OptionType::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  OptionType::where(function($query){});
            if(!empty($request->input('search.value'))){
                $search = $request->input('search.value');
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%");
                });

                $totalFiltered = OptionType::where(function($query) use ($search){
                        $query->where('name','LIKE',"%{$search}%");
                    })->count();
            }

            $models = $models->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get();

            $data = $models->toArray();

            if(!empty($data)){
                for($i = 0; $i < count($data); $i++){
                    $data[$i]['is_active'] = $data[$i]['is_active'] == 1 ? 
                    '<i class="fa fa-check text-success">'
                    :
                    '<i class="fa fa-close text-danger">';
                    
                    /*$data[$i]['action'] = '
                    <a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    <a href="'.route($this->page.'.delete',[$data[$i]['id']]).'" data-name="'.$data[$i]['name'].'" class="btn-delete" data-toggle="tooltip" data-placement="top" title="Close">
                        <i class="fa fa-close color-danger"></i>
                    </a>
                    ';*/
                    $data[$i]['action'] = '';
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

        $viewtarget = "pages.".$this->page.".add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request)
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $param_type = $request->all();
        $options = $param_type['options'];
        $user_id = Session::get('user')->id;

        unset($param_type['_token']);
        unset($param_type['options']);

        // check if vendor exist
        $check = OptionType::where('name',$request->name)->first();
        if($check){
            Session::flash('message.error', ucwords($this->page).' already exists!');
            return redirect()->back()->withInput();
        }
        
        $param_type['is_active'] = $param_type['is_active'] === 'true' ? true: false;
        $param_type['created_by'] = $user_id;

        $result = OptionType::create($param_type);

        if($result->id){
            $data = array();
            for($i = 0; $i < count($options); $i++){
                $data['option_type_id'] = $result->id;
                $data['name'] = $options[$i];
                $data['created_by'] = $user_id;
                $data['created_at'] = $now;
                $data['updated_at'] = $now;
                Option::insert($data);
            }
        }else{
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back()->withInput();
        }

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
        Vendor::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($data->delete()){
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }

}
