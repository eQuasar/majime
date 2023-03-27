<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function checkLogin()
    {
        if(Auth::user()->role_id == User::ROLE_ID_ADMIN)
        {
            return redirect()->intended(RouteServiceProvider::ADMIN);
        }
        else if(Auth::user()->role_id == User::ROLE_ID_VENDOR)
        {
            // dd(RouteServiceProvider::SERVICE);
            return redirect()->intended(RouteServiceProvider::VENDOR);
        }
        else if(Auth::user()->role_id == User::ROLE_ID_ACCOUNTS)
        {
            // dd(RouteServiceProvider::SERVICE);
            return redirect()->intended(RouteServiceProvider::ACCOUNTS);
        }
     
        else
        {
            return redirect('login');
        }
    }
}
