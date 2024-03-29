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
use App\Models\Log;
use DB;

class ProductController extends Controller {

    private $page = 'product';
    public $menu;
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->menu = $request->get('menu');
            return $next($request);
        });
    }

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords($this->page).' Menu';
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
        if ($request->ajax()) {
            $columns = array(
                0 =>'name',
                1 =>'description',
                2 =>'is_active',
                3 =>'id',
            );
            
            $role = $request->get('role');

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

    public function add() {
        $param = array();
        $param['_title'] = 'Deluna | Add '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['_page'] = $this->page;
        $param['_sidebar'] = $this->menu;
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
            'weight' => 'required',
            'code' => 'required',
            'category' => 'required',
            'option_0' => 'required',
            'type_0' => 'required',
            'stocks' => 'required',
            'price' => 'required',
            'sales_price' => 'required',
            'is_active' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        if(count($request->option_0) != count($request->price) && count($request->option_0) != count($request->sales_price) && count($request->option_0) != count($request->stocks)){
            Session::flash('message.error', 'Options and Stocks not valid!');
            return redirect()->back()->withInput();
        }
        // check if product exist
        $check = Product::where(['code' => $request->code, 'deleted_at' => NULL])->first();
        if($check){
            Session::flash('message.error', 'Product Code already exists!');
            return redirect()->back();
        }
        DB::beginTransaction();
        try {
            // create new product
            $product = new Product;
            $product->name = $request->name;
            $product->weight = $request->weight;
            $product->code = $request->code;
            $product->category_id = $request->category;
            $product->description = $request->description ? $request->description : '';
            $product->type_1 = $request->type_0;
            $product->type_2 = $request->type_1 ? $request->type_1 : null;
            $product->created_by = Session::get('user')->id;
            $product->is_active = $request->is_active === 'true' ? true: false;
            $product->save();
            // insert stocks
            for($i=0; $i<count($request->option_0); $i++){
                $arr = [
                    'product_id' => $product->id,
                    'option_1' => $request->option_0[$i]
                ];
                $stock = Stock::firstOrNew($arr);
                if($request->option_1)
                    $stock->option_2 = $request->option_1[$i];
                $stock->stock = $stock->stock + $request->stocks[$i];
                $stock->price = $request->price[$i];
                $stock->sales_price = $request->sales_price[$i];
                $stock->created_by = Session::get('user')->id;
                $stock->save();
                // insert log
                $log = new Log;
                $log->reference_id = $stock->id;
                $log->type = 'new';
                $log->stock_from = 0;
                $log->stock_to = $stock->stock;
                $log->created_by = Session::get('user')->id;
                $log->save();
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
        $param = array();
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];
        $param['_sidebar'] = $this->menu;
        $param['_auth'] = session()->get('user');

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
            'weight' => 'required',
            'code' => 'required',
            'category' => 'required',
            'is_active' => 'required',
        ];
        $custom = [
            'required' => 'The :attribute field is required.',
        ];
        $validator = Validator::make($request->all(), $rules, $custom);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        if($request->option_0){
            if(count($request->option_0) != count($request->option_1) && count($request->option_0) != count($request->stocks)){
                Session::flash('message.error', 'Options and Stocks not valid!');
                return redirect()->back()->withInput();
            }
        }
        // check if product exists
        $product = Product::find($slug);
        if(!$product){
            Session::flash('message.error', ucwords($this->page).' doesn\'t exists!');
            return redirect()->back();
        }
        // check if product code exists
        $check = Product::where(['code' => $request->code, 'deleted_at' => NULL, ['id', '!=', $slug] ])->first();
        if($check){
            Session::flash('message.error', 'Product Code already exists!');
            return redirect()->back();
        }
        // update category
        DB::beginTransaction();
        try {
            $array = [
                'name' => $request->name,
                'weight' => $request->weight,
                'code' => $request->code,
                'category_id' => $request->category,
                'description' => $request->description ? $request->description : '',
                'is_active' => $request->is_active === 'true' ? true: false,
                'updated_by' => Session::get('user')->id,
            ];
            $product = Product::where('id', $slug)->update($array);
            if($request->option_0){
                for($i=0; $i<count($request->option_0); $i++){
                    $arr = [
                        'product_id' => $slug,
                        'option_1'=> $request->option_0[$i]
                    ];
                    if($request->option_1)
                        $arr['option_2'] = $request->option_1[$i];
                    $stock = Stock::firstOrNew($arr);
                    $stock_from = $stock->stock;
                    if($request->option_1)
                        $stock->option_2 = $request->option_1[$i];
                    $stock->stock = $stock_from + $request->stocks[$i];
                    $stock->price = $request->price[$i];
                    $stock->sales_price = $request->sales_price[$i];
                    $stock->updated_by = Session::get('user')->id;
                    $stock->save();
                    // insert log
                    $log = new Log;
                    $log->reference_id = $stock->id;
                    $log->type = 'update';
                    $log->stock_from = $stock_from;
                    $log->stock_to = $stock->stock;
                    $log->created_by = Session::get('user')->id;
                    $log->save();
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
        $stock_from = $stock->stock;
        // update
        $stock->option_1 = $request->opt_0;
        $stock->option_2 = $request->opt_1;
        $stock->stock = $request->stock;
        $stock->price = $request->price ? str_replace([',', '.'], '', $request->price) : 0;
        $stock->sales_price = $request->sales_price ? str_replace([',', '.'], '', $request->sales_price) : 0;
        $stock->updated_by =Session::get('user')->id;

        if($stock->save()){
            // insert log
            $log = new Log;
            $log->reference_id = $stock->id;
            $log->type = 'update';
            $log->stock_from = $stock_from;
            $log->stock_to = $stock->stock;
            $log->created_by = Session::get('user')->id;
            $log->save();

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
        $stock_from = $stock->stock;
        if($stock->save()){
            $stock->delete();
            // insert log
            $log = new Log;
            $log->reference_id = $stock->id;
            $log->type = 'delete_stock';
            $log->stock_from = $stock_from;
            $log->stock_to = 0;
            $log->created_by = Session::get('user')->id;
            $log->save();
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
