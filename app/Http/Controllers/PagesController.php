<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PagesController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //
        return view('welcome');
    }
    public function admin(Request $request)
    {
        //
        return view('admin');
    }
    public function vendor(Request $request)
    {
        //
        return view('vendor');
    }
    public function accounts(Request $request)
    {
        //
        return view('accounts');
    }
}
