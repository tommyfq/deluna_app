<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller {

    public function __construct(){
	}

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Category Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'Category' => route('category.index')];
        
        $viewtarget = "pages.category.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function get(Request $request)
    {
        if ($request->ajax()) {
            $columns = array(
                0 =>'name',
                1 =>'type',
                2 =>'is_active',
                3 =>'id',
            );

            $totalData = Category::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Category::where(function($query){
                $query->where('is_active',1);
            });
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('type', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Category::where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('type', 'LIKE',"%{$search}%");
                    })
                    ->where(function($query){
                        $query->where('is_active',1);
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
                    <a href="'.route('category.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    <a href="'.route('category.delete',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Close">
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
        $param['_title'] = 'Deluna | Add Category';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'Category' => route('category.index'), 'Add' => route('category.add')];
        $param['type'] = Category::TYPE;
        
        $viewtarget = "pages.category.add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request){
        // validation
        $rules = [
            'name' => 'required',
            // 'type' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if category exist
        $check = Category::where('name',$request->name)->first();
        if($check){
            Session::flash('message.error', 'Category already exists!');
            return redirect()->back();
        }
        // create new category
        $category = new Category;
        $category->name = $request->name;
        $category->type = $request->type ? $request->type : 'category';
        $category->created_by = Session::get('user')->id;
        $category->is_active = 1;
        if($category->save()){
            Session::flash('message.success', 'Category has been created!');
            return redirect()->route('category.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function edit($slug) {
        $param = array();
        $param['_title'] = 'Deluna | Edit Category';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), 'Category' => route('category.index'), 'Edit' => route('category.edit',[$slug])];

        $category = Category::where(['id' => $slug, 'is_active' => 1])->first();
        if(!$category){
            Session::flash('message.error', "Data not found!");
            return redirect()->route('category.index');
        }
        $param['data'] = $category;
        $param['type'] = Category::TYPE;
        $viewtarget = "pages.category.edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function update(Request $request, $slug){
        // validation
        $rules = [
            'name' => 'required',
            // 'type' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // check if category exist
        $category = Category::where('is_active',1)->find($slug);
        if(!$category){
            Session::flash('message.error', 'Category doesn\'t exists!');
            return redirect()->back();
        }
        // update category
        $array = [
            'name' => $request->name,
            'type' => $request->type ? $request->type : 'category',
            'updated_by' => Session::get('user')->id,
        ];
        $category = Category::where('id', $slug)->update($array);
        if($category){
            Session::flash('message.success', 'Category has been updated!');
            return redirect()->route('category.index');
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
        // check if category exist
        $category = Category::where('is_active',1)->find($slug);
        if(!$category){
            Session::flash('message.error', 'Category doesn\'t exists!');
            return redirect()->back();
        }
        $delete = Category::where('id',$slug)->update(['deleted_by' => Session::get('user')->id, 'is_active' => 0]);
        if($delete){
            Session::flash('message.success', 'Category has been deleted!');
            return redirect()->route('category.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }
}
