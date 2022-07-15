<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Vendor;
use DataTables;

class VendorController extends Controller {

    public function __construct(){}

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Vendors Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'Vendor' => route('vendor.index')];
        
        $viewtarget = "pages.vendor.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function getVendors(Request $request)
    {
        //dd($request);
        if ($request->ajax()) {
            $columns = array(
                0 =>'name',
                1 =>'slug',
                2 =>'address',
                3 =>'phone',
                4 =>'status',
            );

            $totalData = Vendor::where(function($query){
                $query->where('status',1);
            })->count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Vendor::where(function($query){
                $query->where('status',1);
            });
            if(!empty($request->input('search.value'))){
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('address', 'LIKE',"%{$search}%")
                        ->orWhere('phone', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Vendor::where(function($query) use ($search){
                        $query->where('name','LIKE',"%{$search}%")
                            ->orWhere('address', 'LIKE',"%{$search}%")
                            ->orWhere('phone', 'LIKE',"%{$search}%");
                    })
                    ->where(function($query){
                        $query->where('status',1);
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
                    $data[$i]['action'] = '
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    <a href="#" data-toggle="tooltip" data-placement="top" title="Close">
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
        $param['_title'] = 'Deluna | Add Vendor';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'Vendor' => route('vendor.index'), 'Add' => route('vendor.add')];
        
        $viewtarget = "pages.vendor.add";
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
