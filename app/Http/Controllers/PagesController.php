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
    public function service(Request $request)
    {
        //
        return view('service');
    }
    public function groomer(Request $request)
    {
        //
        return view('groomer');
    }
    public function client(Request $request)
    {
        //
        return view('client');
    }
}
