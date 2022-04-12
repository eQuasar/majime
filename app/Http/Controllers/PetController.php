<?php

namespace App\Http\Controllers;

use App\Models\Pet;
use App\Models\Client;
use App\Models\Appointment;
use App\Models\AppointmentService;
use Auth;
use Illuminate\Http\Request;
use App\Http\Resources\PetResource;
use Intervention\Image\ImageManagerStatic as Image;

class PetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        if($request->type == 'get_pet')
        {
            $pets= Pet::where('client_id','=',(int)$request->client_id)->get();
            return PetResource::collection($pets);
        }
        else
        {


            $request->validate([
                'name' => 'required|string|max:255',
                'pet_breed' => 'required',
                'coat_level' => 'required',
                'pet_cat_id' => 'required',
                'aggressive' => 'required',
                //'image' => 'required|image|mimes:jpeg,png,jpg' 
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
                    
                    $img->save( public_path('uploads/clients/pet/'.$filename ) );
                }
            }
            else
            {
                $filename = '';
            }
            $get_client = Client::find((int)$request->client_id);
            $new_pet = new Pet();
            $new_pet->user_id = $get_client->user_id;
            $new_pet->client_id = $request->client_id;
            $new_pet->pet_cat_id = $request->pet_cat_id;
            $new_pet->dob = $request->dob;
            $new_pet->name = $request->name;
            $new_pet->breed_id = $request->pet_breed;
            $new_pet->aggresive = $request->aggressive;
            $new_pet->coat_level = $request->coat_level;
            
            $new_pet->image = $filename;
            $new_pet->save();
            return new PetResource($new_pet);
        }    
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function show(Pet $pet)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function edit(Pet $pet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pet $pet)
    {
        //
        $request->validate([
            'name' => 'required|string|max:255',
            'pet_breed' => 'required',
            'coat_level' => 'required',
            'pet_cat_id' => 'required',
            'aggressive' => 'required',
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
                
                $img->save( public_path('uploads/clients/pet/'.$filename ) );
            }
        }
        else
        {
            $filename = '';
        }
        $get_client = Client::find((int)$request->client_id);
        $new_pet = Pet::find((int)$pet->id);
        $new_pet->user_id = $get_client->user_id;
        $new_pet->client_id = $request->client_id;
        $new_pet->pet_cat_id = $request->pet_cat_id;
        $new_pet->dob = $request->dob;
        $new_pet->name = $request->name;
        $new_pet->breed_id = $request->pet_breed;
        $new_pet->aggresive = $request->aggressive;
        $new_pet->coat_level = $request->coat_level;
        if($filename != '')
        {
            $new_pet->image = $filename;
        }
        
        $new_pet->save();
        return new PetResource($new_pet);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pet  $pet
     * @return \Illuminate\Http\Response
     */
    public function destroy(Pet $pet)
    {
        //
        $pet = Pet::find((int)$pet->id);
        if($pet != null)
        {
            $appointment = Appointment::where('pet_id','=',(int)$pet->id)->get();
            if(count($appointment) > 0)
            {
                foreach ($appointment as $key => $value) {
                    $service = AppointmentService::where('appointment_id','=',$value->id)->get();
                    if(count($service) > 0)
                    {
                        foreach ($service as $keys => $values) {
                            # code...
                            $values->delete();
                        }
                    }
                    $value->delete();
                }
            }
            $pet->delete();
        }
        return response()->json(['error' => false],200);
    }


    public function clientpet(Request $request)
    {
        $appointment= Pet::where('client_id',(int)$request->client_id)->orderBy('id','asc')->get();
        return PetResource::collection($appointment); 
    }

    public function getmypets(Request $request)
    {
        $pets = Pet::whereIn('id', explode(",", $request->pet_ids))->get();
        return PetResource::collection($pets);
    }
}
