<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\SalesChannel;
use App\Models\Product;
use App\Models\Option;
use App\Models\Stock;
use App\Models\OrderLog;
use App\Models\Log;
use DB;

class OrderController extends Controller {

    private $page = 'order';
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
        $param['_title'] = 'Deluna | '.ucwords($this->page).' Menu';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index')];
        $param['_page'] = $this->page;
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
                0 => 'order_no',
                1 => 'status',
                2 => 'total_price',
                3 => 'discount',
                4 => 'grand_total',
                5 => 'sales_channel',
                6 => 'customer_name',
                7 => 'customer_phone',
                8 => 'address'
            );

            $totalData = Order::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Order::select('tr_order_header.*','msc.name as sales_channel')->leftJoin('ms_sales_channels as msc','msc.id','=','tr_order_header.sales_channel_id');
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('order_no','LIKE',"%{$search}%")
                        ->orWhere('customer_name', 'LIKE',"%{$search}%")
                        ->orWhere('customer_phone', 'LIKE',"%{$search}%")
                        ->orWhere('customer_email', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Order::where(function($query) use ($search){
                    $query->where('order_no','LIKE',"%{$search}%")
                        ->orWhere('customer_name', 'LIKE',"%{$search}%")
                        ->orWhere('customer_phone', 'LIKE',"%{$search}%")
                        ->orWhere('customer_email', 'LIKE',"%{$search}%");
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
                    $data[$i]['order_no'] = '<a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        '.$data[$i]['order_no'].'
                    </a>';
                    $data[$i]['action'] = '
                    <a href="'.route($this->page.'.edit',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Edit">
                        <i class="fa fa-pencil color-muted m-r-5"></i> 
                    </a>
                    ';

                    /* 
                    <a href="'.route($this->page.'.delete',[$data[$i]['id']]).'" data-toggle="tooltip" data-placement="top" title="Close">
                        <i class="fa fa-close color-danger"></i>
                    </a>
                    */
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
        $param['_title'] = 'Deluna | Create '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Add' => route($this->page.'.add')];
        $param['_page'] = $this->page;
        $param['_sales_channel'] = SalesChannel::where('is_active',1)->get();
        $param['_products'] = Product::where('is_active',1)->whereRaw('id IN (SELECT product_id FROM ms_stock_options GROUP BY product_id HAVING SUM(stock) + SUM(stock_reserved) > 0)')->get();
        $param['_sidebar'] = $this->menu;
        // $category = Category::where(['is_active' => 1])->get();
        // $param['_category'] = $category;
        // $opt_type = OptionType::where(['is_active' => 1])->get();
        // $param['_opt_type'] = $opt_type;
        
        $viewtarget = "pages.".$this->page.".add";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    //ajax
    public function get_option_list(Request $request)
    {
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );
        
         if($request->ajax()){
            $param = $request->all();
            $option_element = "";

            //Option 2
            if(array_key_exists("product_id",$param) && array_key_exists("option_1",$param)){
                $product_id = $request['product_id'];
                $product = Product::where('id',$product_id)->first();
                if($product == null){
                    return json_encode(array(
                        "is_ok" => false,
                        'message' => 'Data Product not found'
                    ));
                }

                $option_2 = DB::select('
                    SELECT mso.option_2 as id, mo.name, mso.sales_price
                    FROM ms_stock_options AS mso
                    LEFT JOIN ms_options AS mo ON mo.id = mso.option_2
                    WHERE mso.option_2 IS NOT NULL
                    AND mso.product_id = '.$product_id.'
                    AND mso.option_1 = '.$param['option_1'].'
                    GROUP BY mso.option_2, mo.name, mso.sales_price
                    HAVING SUM(stock) - SUM(stock_reserved) > 0
                ');

                $option1 = null;
                if(array_key_exists("option_1",$param)){
                    $option1 = $param['option_1'];
                }

                $option_element = $this->generateSelectElement($option_2,"type_2",$param['product_id'],$option1);

            }else if(array_key_exists("product_id",$param)){
                $product_id = $param['product_id'];
                $product = Product::where('id',$product_id)->first();
                if($product == null){
                    return json_encode(array(
                        "is_ok" => false,
                        'message' => 'Data Product not found'
                    ));
                }

                if($product->type_2 == null){
                    $option_1 = DB::select('
                        SELECT mso.option_1 as id, mo.name, mso.sales_price
                        FROM ms_stock_options AS mso
                        LEFT JOIN ms_options AS mo ON mo.id = mso.option_1
                        WHERE mso.product_id = '.$product_id.'
                        GROUP BY mso.option_1, mo.name, mso.sales_price
                        HAVING SUM(stock) - SUM(stock_reserved) > 0
                    ');
                }else{
                    $option_1 = DB::select('
                        SELECT mso.option_1 as id, mo.name
                        FROM ms_stock_options AS mso
                        LEFT JOIN ms_options AS mo ON mo.id = mso.option_1
                        WHERE mso.product_id = '.$product_id.'
                        GROUP BY mso.option_1, mo.name
                        HAVING SUM(stock) - SUM(stock_reserved) > 0
                    ');
                }
                
                $option_element = $this->generateSelectElement($option_1,"type_1",$param['product_id']);
            }
            
            return json_encode(array(
                "is_ok" => true,
                "element" => $option_element,
                'message' => 'Successfully load get data'
            ));
            
        }
        return json_encode($response);
    }

    private function generateSelectElement($arrOpt,$type,$productId,$option1 = null){
        if(count($arrOpt) > 0){
            $option = '<select class="form-control '.$type.'-select option-select2" name="option_'.$type.'[]" required>';
            $option .= '<option value="" selected disabled>Please Select Option Product</option>';
            foreach($arrOpt as $opt){
                $price = "";
                $optionData = "";
                if($option1 != null){
                    $optionData = ' data-option-1="'.$option1.'" ';
                }
                if($type == "type_1" && property_exists($opt,"sales_price") || $type == "type_2"){
                    $price = 'data-price="'.$opt->sales_price.'" ';
                }
                $option .= '<option data-product-id="'.$productId.'" '.$price.$optionData.'value="'.$opt->id.'">'.$opt->name.'</option>';
            }
            $option .= '</select>';
            return $option;
        }
        return '<select class="form-control d-none" name="option_'.$type.'[]"><option value="" selected disabled>No Option</option></select>';
    }

    //ajax
    public function get_stock(Request $request){
        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );
        
        if($request->ajax()){
            $param = $request->all();
            DB::enableQueryLog();
            $query = Stock::select('id')->selectRaw('(stock - stock_reserved) as stock')->where('product_id',$param['product_id'])->where('option_1',$param['option_1']);
            if($param['option_2'] == null){
                $query->whereNull('option_2');
            }else{
                $query->where('option_2',$param['option_2']);
            }
            $stock = $query->first();
            //dd(DB::getQueryLog());
            $response = array(
                "is_ok" => true,
                "data" => $stock,
                "message" => "Successfully Load"
            );

        }
        return json_encode($response);
    }

    public function store(Request $request){
        $data = [];
        $products = $request->products;
        $option_type_1 = $request->option_type_1;
        $option_type_2 = $request->option_type_2;
        $stock_id = $request->stock_id;
        $quantity = $request->quantity;

        $today = Carbon::now()->format('Y-m-d');
        $now = Carbon::now()->format('Y-m-d H:i:s');
        $prefixOrder = Carbon::now()->format('ymd');

        $user_id = Session::get('user')->id;

        $response = array(
            "is_ok" => false,
            "message" => "Request is not ajax"
        );

        $isEnoughStock = true;
        //Data Stock for checking available qty
        $dataStock = array();

        //Data generate for insert data
        $dataDetail = array();

        $totalPrice = 0;

        for($i = 0; $i < count($products); $i++){
            $stock = Stock::where('product_id',$products[$i])->where('option_1',$option_type_1[$i])->where('option_2',$option_type_2[$i])->first();
            $availableStock = $stock->stock - $stock->stock_reserved;
            if($quantity[$i] > $availableStock){
                $isEnoughStock = false;
                $dataStock[] = array(
                    "stock_id" => $stock->id,
                    "stock_qty" => $availableStock > 0 ? $availableStock : 0
                );
            }
            
            $dataDetail[] = array(
                'product_id' => (int)$products[$i],
                'product_option_id' => (int)$stock_id[$i],
                'quantity' => (int)$quantity[$i],
                'price' => (int)$stock->sales_price,
                'base_price' => (int)$stock->price,
                'stock_reserved' => (int) $stock->stock_reserved,
                'created_at' => $now,
                'updated_at' => $now
            );

            $totalPrice = $totalPrice + ($quantity[$i] * $stock->sales_price);
        }

        //If not enough stock
        if(!$isEnoughStock){
            $response['message'] = "Not enough Stock";
            $response['data_stock'] = $dataStock;
            return json_encode($response);
        }

        //Generate order id
        
        $incrementOrder = 1;
        $order_no = $prefixOrder.str_pad($incrementOrder,5,"0",STR_PAD_LEFT);

        //Get Last Order to use last running order number
        DB::enableQueryLog();
        $lastOrder = Order::whereRaw('DATE(created_at) = "'.$today.'"')->latest('created_at')->first();

        if($lastOrder){
            $lastIncrement = (int) substr($lastOrder->order_no,6);
            $order_no = $prefixOrder.str_pad(++$lastIncrement,5,"0",STR_PAD_LEFT);
            
        }

        DB::beginTransaction();
        try {
            //create order header
            $dataOrderHeader = array(
                'order_no' => $order_no,
                'status' => Order::STATUS_CREATED,
                'total_price' => $totalPrice,
                'discount' => (int) $request->discount,
                'grand_total' => ($totalPrice - (int) $request->discount),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'address' => $request->address,
                'sales_channel_id' => (int) $request->sales_channel_id,
                'sales_channel_notes' => $request->sales_channel_notes,
                'is_active' => 1,
                'created_at' => $now,
                'created_by' => $user_id,
                'updated_at' => $now,
                'updated_by' => $user_id
            );
            $id = Order::create($dataOrderHeader)->id;
            
            for($i = 0; $i < count($products); $i++){
                $dataDetail[$i]['order_header_id'] = (int)$id;

                //reserved stock
                Stock::where('id',$dataDetail[$i]['product_option_id'])->update(
                    [
                        'stock_reserved' => ($dataDetail[$i]['stock_reserved']+(int)$quantity[$i]),
                        'updated_by' => $user_id
                    ]
                );

                unset($dataDetail[$i]['stock_reserved']);
            }

            OrderDetail::insert($dataDetail);

            OrderLog::create([
                'order_header_id' => $id,
                'status' => ORDER::STATUS_CREATED,
                'created_at' => $now,
                'created_by' => $user_id
            ]);

            DB::commit();

            $response = array(
                "is_ok" => true,
                "message" => "Successfully Create Order ".$order_no
            );
            return json_encode($response);

        }catch(Exception $e){
            DB::rollback();
            $response = array(
                "is_ok" => true,
                "message" => $e->getMessage()
            );
        }
        return json_encode($response);        
        
    }

    public function edit($slug) {
        $param = array();
        $param['_title'] = 'Deluna | Edit '.ucwords($this->page);
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index'), ucwords($this->page) => route($this->page.'.index'), 'Edit' => route($this->page.'.edit',[$slug])];
        $param['_sidebar'] = $this->menu;

        $order = Order::where('id',$slug)->with([
            'order_detail',
            'sales',
            'order_detail.product',
            'order_detail.stock_option.option1',
            'order_detail.stock_option.option2',
            'order_log',
            'order_log.user'
        ])->first();
        $param['_order'] = $order;
        $select = ORDER::STATUS_ARRAY;
        if($order->status != ORDER::STATUS_FINISHED && $order->status != ORDER::STATUS_CANCELED){
            array_splice($select,0,$select[$order->status]);
        }else{
            $select = [];
        }
        $param['_status'] = $select;
        //dd($select);
        //dd(ORDER::STATUS_ARRAY);
        // $product = Product::leftJoin(with(new OptionType)->getTable().' as type1', function($join){
        //                         $join->on('type1.id', with(new Product)->getTable().'.type_1');
        //                     })
        //                     ->leftJoin(with(new OptionType)->getTable().' as type2', function($join){
        //                         $join->on('type2.id', with(new Product)->getTable().'.type_2');
        //                     })
        //                     ->where([with(new Product)->getTable().'.id' => $slug, with(new Product)->getTable().'.deleted_at' => NULL])
        //                     ->select(with(new Product)->getTable().'.*', 'type1.name as type_1_name', 'type2.name as type_2_name')
        //                     ->first();
        // if(!$product){
        //     Session::flash('message.error', "Data not found!");
        //     return redirect()->route($this->page.'.index');
        // }
        // $category = Category::where(['is_active' => 1])->get();
        // $param['_category'] = $category;
        // $opt_type = OptionType::where(['is_active' => 1])->get();
        // $param['_opt_type'] = $opt_type;
        // $option_1 = Option::where(['option_type_id' => $product->type_1, 'deleted_at' => NULL])->get(['id', 'name']);
        // $option_2 = Option::where(['option_type_id' => $product->type_2, 'deleted_at' => NULL])->get(['id', 'name']);
        // $param['_option_1'] = $option_1;
        // $param['_option_2'] = $option_2;
        // $stock = Stock::where('product_id', $product->id)->get();
        // $param['_stock'] = $stock;
        // $param['data'] = $product;
        $param['_page'] = $this->page;
        $viewtarget = "pages.".$this->page.".edit";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

    public function update(Request $request, $slug){
        $response = array(
            'is_ok' => false,
            'message' => 'Request is not ajax'
        );

        $user_id = Session::get('user')->id;
        $now = Carbon::now()->format('Y-m-d H:i:s');

        if($request->ajax()){
            DB::beginTransaction();
            try{
                $order = Order::where('id',$slug)->first();
                $order->status = $request->status;
                $order->updated_by = $user_id;
                $order->sales_channel_notes = $request->sales_channel_notes;
                $order->updated_at = $now;
                $order->save();

                if($request->status == ORDER::STATUS_CANCELED || $request->status == ORDER::STATUS_FINISHED){
                    foreach($order->order_detail as $detail){
                        $stock = Stock::where('id',$detail->product_option_id)->first();
                        $stock->stock_reserved = $stock->stock_reserved - $detail->quantity;

                        if($request->status == ORDER::STATUS_FINISHED){
                            Log::create([
                                'reference_id' => $stock->id,
                                'type' => 'update',
                                'stock_from' => $stock->stock,
                                'stock_to' => $stock->stock - $detail->quantity,
                                'created_by' => $user_id
                            ]);

                            $stock->stock = $stock->stock - $detail->quantity;
                            
                        }

                        if($request->status == ORDER::STATUS_CANCELED){
                            $stock->stock = $stock->stock + $detail->quantity;
                        }

                        $stock->save();

                    }
                }

                OrderLog::create([
                    'order_header_id' => $slug,
                    'status' => $request->status,
                    'created_by' => $user_id
                ]);
                

                DB::commit();

                return json_encode([
                    'is_ok' => true,
                    'message' => 'Successfully update status order '.$order->order_no. " to ".$request->status
                ]);
            }catch(Exception $e){
                DB::rollback();

                return json_encode([
                    'is_ok' => false,
                    'message' => $e->getMessage()
                ]);
            }
            
            
        }

        return json_encode($response);
        
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
