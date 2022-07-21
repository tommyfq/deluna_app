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
                    
                    $data[$i]['action'] = '
                        <a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                            <i class="fa fa-pencil color-muted m-r-5"></i> 
                        </a>
                        <a href="'.route($this->page.'.delete',[$data[$i]['id']]).'" data-name="'.$data[$i]['name'].'" class="btn-delete" data-toggle="tooltip" data-placement="top" title="Close">
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
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];
        $param['_page'] = $this->page;

        $selected = OptionType::where('id', $slug)->with(['option'])->first();
        if(!$selected){
            Session::flash('message.error', "Data not found!");
            return redirect()->route($this->page.'.index');
        }
        $selected->is_active = $selected->is_active == 1 ? 'true' : 'false';
        $param['data'] = $selected;
        $viewtarget = "pages.".$this->page.".edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }
    
    public function update(Request $request, $slug)
    {
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );

        $now = Carbon::now()->format('Y-m-d H:i:s');
        
        $param = $request->all();
        $options = [];
        if(array_key_exists('options',$param)) $options = $param['options'];
        $opt_id = $request->opt_id;
        $opt_name = $request->opt_name;
        $user_id = Session::get('user')->id;

        if($request->ajax()){

            $option_type = OptionType::find($slug);
            if(!$option_type){
                return json_encode(array(
                    "is_ok" => false,
                    "message" => 'Option Type '.$param['name'].' doesn\'t exists!'
                ));
            }

            unset($param['_token']);
            unset($param['_method']);
            unset($param['options']);
            unset($param['opt_id']);
            unset($param['opt_name']);
            
            $param['is_active'] = $param['is_active'] === 'true' ? true: false;
            $param['updated_by'] = Session::get('user')->id;

            // update option type
            $result = OptionType::where('id', $slug)->update($param);
            
            if($result){
                //Update existing 
                for($i = 0; $i < count($opt_id);$i++){
                    $exist_opt = Option::where('name',$opt_name[$i])->where('id','!=',$opt_id[$i])->first();
                    if($exist_opt){
                        return json_encode(array(
                            "is_ok" => false,
                            "message" => 'Option '.$opt_name[$i].' already exist'
                        ));
                    }
                    Option::where('id',(int)$opt_id[$i])->update(['name'=>$opt_name[$i]]);
                }

                //Add new data
                foreach($options as $opt){
                    $exist_opt = Option::where('name',$opt)->where('option_type_id',$slug)->first();
                    if($exist_opt){
                        return json_encode(array(
                            "is_ok" => false,
                            "message" => 'Option '.$opt.' already exist'
                        ));
                    }
                    $data = array();
                    $data['option_type_id'] = $slug;
                    $data['name'] = $opt;
                    $data['created_by'] = $user_id;
                    $data['created_at'] = $now;
                    Option::insert($data);
                }
                return json_encode(array(
                    "is_ok" => true,
                    "message" => 'Option Type '.$param['name'].' has been updated!'
                ));
            } else {
                return json_encode(array(
                    "is_ok" => false,
                    "message" => 'Sorry there is an error while saving the data!'.$param['name']
                ));
            }
        }
    }

    public function delete($slug)
    {
        if(!$slug){
            Session::flash('message.error', 'No data selected!');
            return redirect()->back();
        }
        // check if user exist
        $data = OptionType::find($slug);
        if(!$data){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        OptionType::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($data->delete()){
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }

    public function update_option(Request $request, $slug)
    {
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );

        if($request->ajax()){
            $now = Carbon::now()->format('Y-m-d H:i:s');
            $check = Option::
                where('name',$request->name)
                ->where('id','!=',$slug)
                ->where('option_type_id',$request->option_type_id)
                ->first();

            if(!$check){
                $response = array(
                    "is_ok" => false,
                    "message" => "Already exist"
                );
            }

            try{
                $update = Option::where('id',$slug)->update(
                    [
                        'name'=>$request->name,
                        'updated_by'=>Session::get('user')->id,
                        'updated_at'=>$now
                    ]
                );
                if($update){
                    $response = array(
                        "is_ok" => true,
                        "message" => "Successfully saved"
                    );
                }else{
                    $response = array(
                        "is_ok" => false,
                        "message" => "Failed"
                    );
                }
            }catch(Exception $e){
                $response = array(
                    "is_ok" => false,
                    "message" => "Failed"
                );
            }
            
        }

        return json_encode($response);
    }

    public function delete_option(Request $request, $slug)
    {
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );

        if($request->ajax()){
            $data = Option::find($slug);
            if(!$data){
                return json_encode(array(
                    "is_ok" => false,
                    'message' => 'Option '.$request->name.' not find'
                ));
            }
            try{
                Option::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
                if($data->delete()){
                    return json_encode(array(
                        "is_ok" => true,
                        "option_id" => $data->id,
                        'message' => 'Successfully Delete '.$request->name
                    ));
                }else{
                    return json_encode(array(
                        "is_ok" => false,
                        'message' => 'Sorry there is an error while delete the data '.$request->name.' !'
                    ));
                }
            }catch(Exception $e){
                return json_encode(array(
                    "is_ok" => false,
                    'message' => $e->getMessage()
                ));
            }
        }

        return json_encode($response);
    }

}
