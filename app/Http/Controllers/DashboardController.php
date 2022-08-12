<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

use App\Models\Order;
use App\Models\Product;
use App\Models\Stock;

class DashboardController extends Controller {
    
    public $menu;
    public function __construct(){
        $this->middleware(function ($request, $next) {
            $this->menu = $request->get('menu');
            return $next($request);
        });
    }

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Dashboard';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index')];
        $param['_sidebar'] = $this->menu;

        $total_order = Order::sum('total_price');
        $total_order_today = Order::where('created_at', '>=', date('Y-m-d'))->sum('total_price');
        $total_product = Product::count('id');
        $group_stock = Product::leftJoin(with(new Stock)->getTable().' as s', function($join){
                                    $join->on('s.product_id', with(new Product)->getTable().'.id');
                                })
                                ->groupBy(with(new Product)->getTable().'.id', with(new Product)->getTable().'.name')
                                ->get([with(new Product)->getTable().'.id', with(new Product)->getTable().'.name', DB::raw('sum(s.stock) as total_stock')]);

        $param['total_order'] = $total_order;
        $param['total_order_today'] = $total_order_today;
        $param['total_product'] = $total_product;
        $param['group_stock'] = $group_stock;
        
        $viewtarget = "pages.dashboard.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

}
