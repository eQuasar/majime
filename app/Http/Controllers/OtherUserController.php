<?php

namespace App\Http\Controllers;

use App\Models\OtherUser;
use Illuminate\Http\Request;
use App\Models\User;
use Response;
use Illuminate\Support\Facades\File; 
use Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Resources\OtherUserResource;
use Illuminate\Validation\Rules\Password;

class OtherUserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $users = OtherUser::all();
        return OtherUserResource::collection($users);
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
        if($request->type == 'get')
        {
            $other_user =OtherUser::where('role_id','=',$request->role_id)->get();
            return OtherUserResource::collection($other_user);
        }
        else
        {
            $request->validate([
                'full_name' => 'required|string|max:255',
                'address' => 'required|max:255',
                'phone' => 'required|digits:10|unique:users',
                'email' => 'required|email|unique:users',
                'gender' => 'required',
                'dob' => 'required', 
                'country_id' => 'required', 
                'state_id' => 'required', 
                'city_id' => 'required', 
                'area_id' => 'required', 
                'pincode' => 'required', 
                'password' => 'required', 
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
                    
                    $img->save( public_path('uploads/otheruser/'.$filename ) );
                }
            }
            else
            {
                $filename = '';
            }
            $new_user = new User();
            $new_user->name = $request->full_name;
            $new_user->email = $request->email;
            $new_user->phone = $request->phone;
            $new_user->role_id = $request->role_id;
            $new_user->password = Hash::make($request->password);
            $new_user->save();
            $other_user = new OtherUser();
            $other_user->user_id = $new_user->id;
            $other_user->gender = $request->gender;
            $other_user->dob = date('Y-m-d',strtotime($request->dob));
            $other_user->country_id = $request->country_id;
            $other_user->state_id = $request->state_id;
            $other_user->city_id = $request->city_id;
            $other_user->role_id = $request->role_id;
            $other_user->area_id = $request->area_id;
            $other_user->pincode = $request->pincode;
            $other_user->address = $request->address;
            //get zone
            $other_user->image = $filename;
            $other_user->save();
            return response()->json(['error' => false,'data' => $other_user],200);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\OtherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function show(OtherUser $otheruser)
    {
        //
        $client =OtherUser::with('user')->where('id','=',$otheruser->id)->get()->first();
        // dd($client);
        return new OtherUserResource($client);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\OtherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function edit(OtherUser $otherUser)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\OtherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, OtherUser $otheruser)
    {
        $id = (int)$otheruser->id;
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|max:255',
            'phone' => 'required|digits:10|unique:users,phone,' .$request->user_id,
            'email' => 'required|email|unique:users,email,' .$request->user_id,
            'gender' => 'required',
            'dob' => 'required', 
            'country_id' => 'required', 
            'state_id' => 'required', 
            'city_id' => 'required', 
            'area_id' => 'required', 
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
                
                $img->save( public_path('uploads/otheruser/'.$filename ) );
            }
        }
        else
        {
            $filename='';
        }
        $new_user = User::find((int)$request->user_id);
        $new_user->name = $request->name;
        $new_user->email = $request->email;
        $new_user->phone = $request->phone;
        // $new_user->role_id = $request->role_id;
        $new_user->password = Hash::make($request->password);
        $new_user->save();
        $new_client = OtherUser::find((int)$id);
        $new_client->user_id = $new_user->id;
        $new_client->gender = $request->gender;
        $new_client->dob = date('Y-m-d',strtotime($request->dob));
        $new_client->country_id = $request->country_id;
        $new_client->state_id = $request->state_id;
        $new_client->city_id = $request->city_id;
        $new_client->area_id = $request->area_id;
        $new_client->pincode = $request->pincode;
        $new_client->address = $request->address;
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
     * @param  \App\Models\OtherUser  $otherUser
     * @return \Illuminate\Http\Response
     */
    public function destroy(OtherUser $otherUser)
    {
        //
    }
}
