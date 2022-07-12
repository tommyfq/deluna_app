<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller {

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Dashboard';
        $param['_breadcrumbs'] = ['Dashboard' => route('dashboard.index')];
        
        $viewtarget = "pages.dashboard.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.master', $param);
    }

}
