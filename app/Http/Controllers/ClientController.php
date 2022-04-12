<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ZoneArea;
use App\Models\User;
use Illuminate\Http\Request;
use Response;
use Illuminate\Support\Facades\File; 
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Resources\ClientResource;
class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $clients = Client::with('user','pet.breed')->get();
        return ClientResource::collection($clients);
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
        //
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|digits:10|unique:users',
            'email' => 'unique:users',
            'gender' => 'required',
            'country_id' => 'required', 
            'state_id' => 'required', 
            'city_id' => 'required', 
            'area_id' => 'required', 
            'pincode' => 'required', 
            'alternate_address' => 'max:255',
            // 'alternate_phone' => 'digits:10'
            // 'image' => 'required|image|mimes:jpeg,png,jpg' 
        ]);
        if($request->hasFile('image'))
        {  
            $allowedExtns = array('jpg', 'jpeg', 'png');
            $stuImg = $request->file('image');
            $stuImgName = $stuImg->getClientOriginalName();
            $stuImgExt = $stuImg->getClientOriginalExtension();
            $filename = time() . ".$stuImgExt";
            
            if(in_array($stuImgExt, $allowedExtns)){
                $img = Image::make($stuImg);
                
                $img->save( public_path('uploads/clients/profile/'.$filename ) );
            }
        }
        else
        {
            $filename = '';
        }
        $new_user = new User();
        $new_user->name = $request->full_name;
        if($request->email != ''){
            $new_user->email = $request->email;
        }else{
            $new_user->email = $request->phone."@pupping.in";
        }
        $new_user->phone = $request->phone;
        $new_user->role_id = 3;
        $new_user->password = Hash::make(123456);
        $new_user->save();
        $new_client = new Client();
        $new_client->user_id = $new_user->id;
        $new_client->gender = $request->gender;
        $new_client->dob = date('Y-m-d',strtotime($request->dob));
        $new_client->country_id = $request->country_id;
        $new_client->state_id = $request->state_id;
        $new_client->city_id = $request->city_id;
        $new_client->area_id = $request->area_id;
        $new_client->pincode = $request->pincode;
        $new_client->address = $request->address;
        $new_client->alternate_address = $request->alternate_address;
        $new_client->alternate_phone = $request->alternate_phone;
        //get zone
        // $data = \DB::table("zones")
        //             ->select("zones.*")
        //             ->whereRaw("find_in_set('".$request->area_id."',zones.areas)")
        //             ->get()->first();
        $data = ZoneArea::where('area_id','=',$request->area_id)->get()->first();            
                    
        if($data != null)
        {
            $new_client->zone_id = $data->id;
        }
        else
        {
            $new_client->zone_id = 0;
        }
        $new_client->image = $filename;
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show(Client $client)
    {
        //
        $client =Client::with('user')->where('id','=',$client->id)->get()->first();
        return new ClientResource($client);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Client $client)
    {
        // dd($client->id);
        $id = (int)$client->id;
        $request->validate([
            'full_name' => 'required|string|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|digits:10|unique:users,phone,' .$request->user_id,
            'email' => 'required|email|unique:users,email,' .$request->user_id,
            'gender' => 'required',
            'country_id' => 'required', 
            'state_id' => 'required', 
            'city_id' => 'required', 
            'area_id' => 'required', 
            'zone_id' => 'required',
            'pincode' => 'required',  
            'alternate_address' => 'max:255',
            // 'alternate_phone' => 'digits:12'
            // 'image' => 'required|image|mimes:jpeg,png,jpg' 
        ]);
        if($request->hasFile('image'))
        {  
            $allowedExtns = array('jpg', 'jpeg', 'png');
            $stuImg = $request->file('image');
            $stuImgName = $stuImg->getClientOriginalName();
            $stuImgExt = $stuImg->getClientOriginalExtension();
            $filename = time() . ".$stuImgExt";
            
            if(in_array($stuImgExt, $allowedExtns)){
                $img = Image::make($stuImg);
                
                $img->save( public_path('uploads/clients/profile/'.$filename ) );
            }
        }
        else
        {
            $filename='';
        }
        $new_user = User::find((int)$request->user_id);
        $new_user->name = $request->full_name;
        $new_user->email = $request->email;
        $new_user->phone = $request->phone;
        // $new_user->role_id = 2;
        // $new_user->password = Hash::make(123456);
        $new_user->save();
        $new_client = Client::find((int)$id);
        $new_client->user_id = $new_user->id;
        $new_client->gender = $request->gender;
        $new_client->dob = date('Y-m-d',strtotime($request->dob));
        $new_client->country_id = $request->country_id;
        $new_client->state_id = $request->state_id;
        $new_client->city_id = $request->city_id;
        $new_client->area_id = $request->area_id;
        $new_client->pincode = $request->pincode;
        $data = ZoneArea::where('area_id','=',$request->area_id)->get()->first();            
                    
        if($data != null)
        {
            $new_client->zone_id = $data->id;
        }
        else
        {
            $new_client->zone_id = 0;
        }
        // $new_client->zone_id = $request->zone_id;

        $new_client->address = $request->address;
        $new_client->alternate_address = $request->alternate_address;
        $new_client->alternate_phone = $request->alternate_phone;
        if($filename != '')
        {
            $new_client->image = $filename;
        }
        $new_client->save();
        return response()->json(['error' => false,'data' => $new_client],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client)
    {
        //
    }

    public function countclients(){
        $clients = Client::all();
        return $clients->count();
    }

    public function getclientinfo(Request $request){
        // dd($request);
        $data = Client::where('user_id','=',$request->user_id)->get()->first(); 
        return response()->json(['error' => false,'data' => $data],200);
    }
}
