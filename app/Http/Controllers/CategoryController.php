<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Category;

class CategoryController extends Controller {

    private $page = 'category';
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

            $models =  Category::where(function($query){});
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('type', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Category::where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('type', 'LIKE',"%{$search}%");
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
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['type'] = Category::TYPE;
        $param['_page'] = $this->page;
        
        $viewtarget = "pages.".$this->page.".add";
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
        $check = Category::where(['name' => $request->name, 'deleted_at' => NULL])->first();
        if($check){
            Session::flash('message.error', ucwords($this->page).' already exists!');
            return redirect()->back();
        }
        // create new category
        $category = new Category;
        $category->name = $request->name;
        $category->type = $request->type ? $request->type : $this->page;
        $category->created_by = Session::get('user')->id;
        $category->is_active = $request->is_active === 'true' ? true: false;
        if($category->save()){
            Session::flash('message.success', ucwords($this->page).' has been created!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function edit($slug) {
        $param = array();
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];

        $category = Category::where(['id' => $slug])->first();
        if(!$category){
            Session::flash('message.error', "Data not found!");
            return redirect()->route($this->page.'.index');
        }
        $param['data'] = $category;
        $param['type'] = Category::TYPE;
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
        $category = Category::find($slug);
        if(!$category){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        // update category
        $array = [
            'name' => $request->name,
            'type' => $request->type ? $request->type : $this->page,
            'is_active' => $request->is_active === 'true' ? true: false,
            'updated_by' => Session::get('user')->id,
        ];
        $category = Category::where('id', $slug)->update($array);
        if($category){
            Session::flash('message.success', ucwords($this->page).' has been updated!');
            return redirect()->route($this->page.'.index');
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
        $category = Category::find($slug);
        if(!$category){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        Category::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($category->delete()){
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }
}
