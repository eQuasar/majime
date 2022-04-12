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
            return redirect()->intended(RouteServiceProvider::HOME);
        }
        else if(Auth::user()->role_id == User::ROLE_ID_SERVICE)
        {
            // dd(RouteServiceProvider::SERVICE);
            return redirect()->intended(RouteServiceProvider::SERVICE);
        }
        else if(Auth::user()->role_id == User::ROLE_ID_GROMMER)
        {
            // dd(RouteServiceProvider::SERVICE);
            return redirect()->intended(RouteServiceProvider::GROOMER);
        }
        else if(Auth::user()->role_id == User::ROLE_ID_CLIENT)
        {
            return redirect()->intended(RouteServiceProvider::CLIENT);
        }
        else
        {
            return redirect('login');
        }
    }
}
