<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use DB;
use App\Models\User;
use App\Models\Vendors;

class VendorsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $breed= Vendors::all();
        return $breed;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                'name' => 'required|string|max:255',
                'url' => 'required|string|max:255',
            ]);

            $userinfo = DB::table("users")
                ->where("phone", $request->user_phone)
                ->orWhere("email", $request->email)->get()->toArray();

            if(isset($userinfo)){
                $uid = $userinfo[0]->id;
                $vendors = DB::table("vendors")
                ->where("user_id", $uid)->get()->toArray();
                if(isset($vendors)){
                    $import = new Vendors();
                    $import->user_id = $uid;
                    $import->name = $request->name;
                    $import->url = $request->url;
                    $import->consumer_key = $request->consumer_key;
                    $import->secret_key = $request->secret_key;
                    $import->save();
                    return response()->json(['error' => false,'data' => $import,'msg' => "Vendor created successfully"],200);
                }else{
                    return response()->json(['error' => false,'msg' => "Vendor has already created their instance."],200);
                }
            }else{
                $new_user = new User();
                $new_user->name = $request->user_name;
                $new_user->email = $request->email;
                $new_user->phone = $request->user_phone;
                $new_user->role_id = 1;
                $new_user->password = Hash::make(123456);
                $new_user->save();
                $import = new Vendors();
                $import->user_id = $new_user->id;
                $import->name = $request->name;
                $import->url = $request->url;
                $import->consumer_key = $request->consumer_key;
                $import->secret_key = $request->secret_key;
                $import->save();
                return response()->json(['error' => false,'data' => $import,'msg' => "Vendor created successfully"],200);
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Vendors  $Vendors
     * @return \Illuminate\Http\Response
     */
    public function show(Vendors $Vendors)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Vendors  $Vendors
     * @return \Illuminate\Http\Response
     */
    public function edit(Vendors $Vendors)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Vendors  $Vendors
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Vendors $Vendors)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Vendors  $Vendors
     * @return \Illuminate\Http\Response
     */
    public function destroy(Vendors $Vendors)
    {
        //
    }
}
