<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
        
        $viewtarget = "pages.dashboard.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

}
