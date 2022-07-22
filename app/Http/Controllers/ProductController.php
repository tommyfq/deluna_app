<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\Stock;
use App\Models\Category;
use App\Models\Option;
use App\Models\OptionType;
use DB;

class ProductController extends Controller {

    private $page = 'product';
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
                1 =>'description',
                2 =>'is_active',
                3 =>'id',
            );

            $totalData = Product::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Product::where(function($query){});
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('description', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Product::where(function($query) use ($search){
                    $query->where('name','LIKE',"%{$search}%")
                        ->orWhere('description', 'LIKE',"%{$search}%");
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
        $param = array();
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['_page'] = $this->page;
        $category = Category::where(['is_active' => 1])->get();
        $param['_category'] = $category;
        $opt_type = OptionType::where(['is_active' => 1])->get();
        $param['_opt_type'] = $opt_type;
        
        $viewtarget = "pages.".$this->page.".add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function store(Request $request){
        // validation
        $rules = [
            'name' => 'required',
            'category' => 'required',
            'option_0' => 'required',
            'option_1' => 'required',
            'type_0' => 'required',
            'type_1' => 'required',
            'stocks' => 'required',
            'is_active' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        if(count($request->option_0) != count($request->option_1) && count($request->option_0) != count($request->stocks)){
            Session::flash('message.error', 'Options and Stocks not valid!');
            return redirect()->back()->withInput();
        }
        // check if category exist
        $check = Product::where(['name' => $request->name, 'deleted_at' => NULL])->first();
        if($check){
            Session::flash('message.error', ucwords($this->page).' already exists!');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            // create new product
            $product = new Product;
            $product->name = $request->name;
            $product->category_id = $request->category;
            $product->description = $request->description ? $request->description : '';
            $product->type_1 = $request->type_0;
            $product->type_2 = $request->type_1;
            $product->created_by = Session::get('user')->id;
            $product->is_active = $request->is_active === 'true' ? true: false;
            $product->save();
            // insert stocks
            for($i=0; $i<count($request->option_0); $i++){
                $arr = [
                    'product_id' => $product->id,
                    'option_1'=> $request->option_0[$i],
                    'option_2' =>  $request->option_1[$i]
                ];
                $stock = Stock::firstOrNew($arr);
                $stock->stock = $stock->stock + $request->stocks[$i];
                $stock->created_by = Session::get('user')->id;
                $stock->save();
            }
            DB::commit();
            Session::flash('message.success', ucwords($this->page).' has been created!');
            return redirect()->route($this->page.'.index');
        } catch (\Exception $e) {
            DB::rollback();
            Session::flash('message.error', 'Sorry there is an error while saving the data!');
            return redirect()->back();
        }
    }

    public function edit($slug) {
        $param = array();
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];

        $product = Product::leftJoin(with(new OptionType)->getTable().' as type1', function($join){
                                $join->on('type1.id', with(new Product)->getTable().'.type_1');
                            })
                            ->leftJoin(with(new OptionType)->getTable().' as type2', function($join){
                                $join->on('type2.id', with(new Product)->getTable().'.type_2');
                            })
                            ->where([with(new Product)->getTable().'.id' => $slug, with(new Product)->getTable().'.deleted_at' => NULL])
                            ->select(with(new Product)->getTable().'.*', 'type1.name as type_1_name', 'type2.name as type_2_name')
                            ->first();
        if(!$product){
            Session::flash('message.error', "Data not found!");
            return redirect()->route($this->page.'.index');
        }
        $category = Category::where(['is_active' => 1])->get();
        $param['_category'] = $category;
        $opt_type = OptionType::where(['is_active' => 1])->get();
        $param['_opt_type'] = $opt_type;
        $option_1 = Option::where(['option_type_id' => $product->type_1, 'deleted_at' => NULL])->get(['id', 'name']);
        $option_2 = Option::where(['option_type_id' => $product->type_2, 'deleted_at' => NULL])->get(['id', 'name']);
        $param['_option_1'] = $option_1;
        $param['_option_2'] = $option_2;
        $stock = Stock::where('product_id', $product->id)->get();
        $param['_stock'] = $stock;
        $param['data'] = $product;
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
            'category' => 'required',
            'option_0' => 'required',
            'option_1' => 'required',
            'type_0' => 'required',
            'type_1' => 'required',
            'stocks' => 'required',
            'is_active' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        if(count($request->option_0) != count($request->option_1) && count($request->option_0) != count($request->stocks)){
            Session::flash('message.error', 'Options and Stocks not valid!');
            return redirect()->back()->withInput();
        }
        // check if category exist
        $product = Product::find($slug);
        if(!$product){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        // update category
        DB::beginTransaction();
        try {
            $array = [
                'name' => $request->name,
                'category_id' => $request->category,
                'description' => $request->description ? $request->description : '',
                'is_active' => $request->is_active === 'true' ? true: false,
                'updated_by' => Session::get('user')->id,
            ];
            $product = Product::where('id', $slug)->update($array);
            for($i=0; $i<count($request->option_0); $i++){
                $arr = [
                    'product_id' => $slug,
                    'option_1'=> $request->option_0[$i],
                    'option_2' =>  $request->option_1[$i]
                ];
                $stock = Stock::firstOrNew($arr);
                $stock->stock = $stock->stock + $request->stocks[$i];
                $stock->updated_by = Session::get('user')->id;
                $stock->save();
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
        $product = Product::find($slug);
        if(!$product){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        Product::where('id',$slug)->update(['deleted_by' => Session::get('user')->id]);
        if($product->delete()){
            // delete all stocks
            Stock::where('product_id', $slug)->update(['deleted_by' => Session::get('user')->id]);
            Stock::where('product_id', $slug)->delete();
            Session::flash('message.success', ucwords($this->page).' has been deleted!');
            return redirect()->route($this->page.'.index');
        } else {
            Session::flash('message.error', 'Sorry there is an error while delete the data!');
            return redirect()->back();
        }
    }

    // ajax here
    public function get_options(Request $request){
        if($request->ajax()){
            if(!$request->id){
                $response = array(
                    "is_ok" => false,
                    "message" => "No data given!"
                );
                return json_encode($response);
            }
            $data = Option::where(['option_type_id' => $request->id])->get(['id', 'name']);
            $response = array(
                "is_ok" => true,
                "message" => "Data retrieved!",
                "data" => $data
            );
            return json_encode($response);
        } else {
            $response = array(
                "is_ok" => false,
                "message" => "Request can't be received!"
            );
            return json_encode($response);
        }
    }

    public function update_stock(Request $request, $slug){
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );
        if(!$request->ajax()){
            return json_encode($response);
        }
        if(!$request->opt_0 || !$request->opt_1 || !$request->stock || !$slug){
            return json_encode(array(
                "is_ok" => false,
                "message" => "Request can't be fulfilled because the data not valid!"
            ));
        }
        // check data
        $stock = Stock::find($slug);
        if(!$stock){
            return json_encode(array(
                "is_ok" => false,
                "message" => "Data not found!"
            ));
        }
        // update
        $stock->option_1 = $request->opt_0;
        $stock->option_2 = $request->opt_1;
        $stock->stock = $request->stock;
        $stock->updated_by =Session::get('user')->id;
        
        if($stock->save()){
            return json_encode(array(
                "is_ok" => true,
                "message" => 'Stock updated!'
            ));
        } else {
            return json_encode(array(
                "is_ok" => false,
                "message" => "There is an error while saving the data!"
            ));
        }

    }

    public function delete_stock(Request $request, $slug){
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );
        if(!$request->ajax()){
            return json_encode($response);
        }
        if(!$slug){
            return json_encode(array(
                "is_ok" => false,
                "message" => "Request can't be fulfilled because the data not valid!"
            ));
        }
        // check data
        $stock = Stock::find($slug);
        if(!$stock){
            return json_encode(array(
                "is_ok" => false,
                "message" => "Data not found!"
            ));
        }
        // update
        $stock->deleted_by =Session::get('user')->id;
        
        if($stock->save()){
            $stock->delete();
            return json_encode(array(
                "is_ok" => true,
                "message" => 'Stock updated!'
            ));
        } else {
            return json_encode(array(
                "is_ok" => false,
                "message" => "There is an error while saving the data!"
            ));
        }

    }
}
