<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthController extends Controller {

    public function index(Request $request) {
        $param = array();
        $param['_title'] = 'Deluna | Login';
        
        $viewtarget = "pages.login.index";
        $content = view($viewtarget, $param);
        $param['CONTENT'] = $content;
        return view('layouts.login', $param);
    }

}
