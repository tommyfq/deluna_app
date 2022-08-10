<?php

namespace App\Http\Controllers;

use Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use App\Models\Product;
use App\Models\Log;
use App\Models\Stock;
use App\Models\Order;
use DB;
use DataTables;

class LogStockController extends Controller {

    private $page = 'log-stock';
    public $menu;
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->menu = $request->get('menu');
            return $next($request);
        });
    }

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | '.ucwords(str_contains($this->page, '-') ? str_replace('-', ' ', $this->page) : $this->page).' Menu';
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
                'id',
                'reference',
                'type',
                'stock_from',
                'stock_to',
                'is_active',
            );

            $totalData = Log::count();
            
            $totalFiltered = $totalData;

            $limit = $request->input('length');
            $start = $request->input('start');
            $order = $columns[$request->input('order.0.column')];
            $dir = $request->input('order.0.dir');
            $search = $request->input('search.value');

            $models =  Log::leftJoin(with(new Stock)->getTable().' as s', function($join){
                                $join->on('s.id', with(new Log)->getTable().'.reference_id');
                            })
                            ->leftJoin(with(new Product)->getTable().' as p', function($join){
                                $join->on('p.id', 's.product_id');
                            })
                            ->leftJoin(with(new Order)->getTable().' as o', function($join){
                                $join->on('o.id', with(new Log)->getTable().'.order_id');
                            });
            if(!empty($search)){
                $models->where(function($query) use ($search){
                    $query->where('p.name','LIKE',"%{$search}%")
                        ->orWhere('o.order_no', 'LIKE',"%{$search}%");
                });

                $totalFiltered = Log::leftJoin(with(new Stock)->getTable().' as s', function($join){
                                        $join->on('s.id', with(new Log)->getTable().'.reference_id');
                                    })
                                    ->leftJoin(with(new Product)->getTable().' as p', function($join){
                                        $join->on('p.id', 's.product_id');
                                    })
                                    ->leftJoin(with(new Order)->getTable().' as o', function($join){
                                        $join->on('o.id', with(new Log)->getTable().'.order_id');
                                    })
                                    ->where(function($query) use ($search){
                                    $query->where('p.name','LIKE',"%{$search}%")
                                        ->orWhere('o.order_no', 'LIKE',"%{$search}%");
                                    })
                                    ->count();
            }

            $models = $models->offset($start)
                    ->limit($limit)
                    ->orderBy($order,$dir)
                    ->get([with(new Log)->getTable().'.*', DB::raw('CASE WHEN o.order_no IS NULL THEN p.name ELSE o.order_no END reference')]);

            $data = $models->toArray();

            $json_data = array(
                "draw"            => intval($request->input('draw')),
                "recordsTotal"    => intval($totalData),
                "recordsFiltered" => intval($totalFiltered),
                "data"            => $data
            );

            return json_encode($json_data);
        }
    }
}
