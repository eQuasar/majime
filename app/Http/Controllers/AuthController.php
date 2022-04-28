<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use DB;
use App\Models\Client;
use App\Models\ZoneArea;
use Response;
use Illuminate\Support\Facades\File; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Resources\ClientResource;

class AuthController extends Controller
{
    /**
     * Write code on Method
     *
     * @return response() 
     */
    
    public function index()
    {
        return view('otplogin');
    }
    public function signup()
    {
        return view('signup');
    }
    public function registerUser(Request $request)
    {
        $userinfo = DB::table("users")
                ->where("phone", $request->phone)
                ->where("email", $request->email)->get()->toArray();

        if(count($userinfo)>0) {
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
            $new_user->name = $request->name;
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
            // $new_client->alternate_address = $request->alternate_address;
            // $new_client->alternate_phone = $request->alternate_phone;
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
            Auth::loginUsingId($new_user->id);
            return response()->json(['error' => false,'data' => $new_client, 'msg' => 'Register User Successfully',"ErrorCode" => "000","ErrorMessage" => "Done"],200);
        }else{
            return response()->json(['error' => true, 'msg' => 'Email/Phone Already used.',"ErrorCode" => -2],200);
        }
    }
    public function sendOTP(Request $request){
        try{
            $phone = $request->get("number");
            $otp = rand(100000, 999999);
            $affected = DB::table('users')
                        ->where('phone', $phone)
                        ->update(['otp' => $otp]);
            //   ->update(['otp_expiry' => $otp]);
        
            if($affected == 1){
            
                $message = "Hi User, your OTP to login is ".$otp.". Thank, Team eQuasar Solutions";
                $message = urlencode($message);
                //$api = "http://cloud.smsindiahub.in/api/mt/SendSMS?APIKey=No30W31Hnkmi7KNd4EIRYg&senderid=IMHAPP&channel=trans&DCS=0&flashsms=0&number=918146116608&text=Hi%20User,%20your%20OTP%20to%20login%20is%20123456.%20Thank,%20Team%20eQuasar%20Solutions";
                
                // $api = "http://cloud.smsindiahub.in/api/mt/SendSMS?APIKey=No30W31Hnkmi7KNd4EIRYg&senderid=IMHAPP&channel=trans&DCS=0&flashsms=0&number=91".$phone."&text=".$message;

                // $response = file_get_contents($api);
                // $response = '{"otp":"'.$otp.'","ErrorCode":"000","ErrorMessage":"Done","JobId":"26354934","MessageData":[{"Number":"918284840500","MessageId":"I3oPTZPgCE6HWTxuYiAIUA","Message":null}]}';

                $response = '{"otp":"'.$otp.'","ErrorCode":"000","ErrorMessage":"Done","JobId":"26354934","MessageData":[{"Number":"918284840500","MessageId":"I3oPTZPgCE6HWTxuYiAIUA","Message":null}]}';

                return $response;
            }
        } catch (Exception $e) {
            return json_encode(["ErrorCode" => "-1", "msg" => $e->getMessage()]);
        }
    }

    public function verifyOTP(Request $request){
        try{
            $phone = $request->get("number");
            $otp = $request->get("otp");
            $userinfo = DB::table("users")
                    ->where("phone", $phone)
                    ->where("otp", $otp)->get()->toArray();
            //var_dump(count($userinfo));
            if(count($userinfo)>0) {
                Auth::loginUsingId($userinfo[0]->id);
                return json_encode(["ErrorCode" => "000", "msg" => "Otp verifification success"]);
            } else {
                return json_encode(["ErrorCode" => "-2", "msg" => "This OTP is invalid"]);
            }
        }  catch (Exception $e) {
            return json_encode(["ErrorCode" => "-1", "msg" => $e->getMessage()]);
        }
    }
}
