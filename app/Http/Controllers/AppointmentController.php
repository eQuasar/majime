<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\AppointmentService;
use App\Models\CancelAppointment;
use App\Models\ProcessAppointment;
use Illuminate\Http\Request;
use App\Models\ServiceCost;
use App\Models\Client;
use App\Models\VehicleZone;
use App\Models\Vehicle;
use App\Models\User;
use App\Models\Pet;
use App\Models\Service;
use App\Models\Breed;
use App\Models\OtherUser;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\ProcessAppointmentResource;
use App\Http\Resources\VehicleResource;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    //listing data use table appointments
    public function index()
    {
        //
        $appointment= Appointment::whereDate('date','>=',date('Y-m-d'))->where('status','!=',5)->where('status','!=',6)->orderBy('date','asc')->get();
        return AppointmentResource::collection($appointment);
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
    //save data use store function
    public function store(Request $request)
    {
        if($request->myserv != ''){
            // dd($request);
            $new_appoint = new Appointment();
            $new_appoint->client_id = $request->client_id;
            $new_appoint->pet_id = $request->pet_id;
            $new_appoint->vehicle_id = $request->vehicle;
            $new_appoint->date = $request->date;
            $new_appoint->time_id = $request->time;
            $new_appoint->total_cost = $request->total_cost;
            if($request->payment_mode != null){
                $new_appoint->payment_mode = $request->payment_mode;
            }
            $new_appoint->status = 1;
            $new_appoint->save();
            $cost = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$request->service_id)->get()->first();
            if($cost != null)
            {
                $new_appoint_service = new AppointmentService();
                $new_appoint_service->appointment_id = $new_appoint->id;
                $new_appoint_service->service_id = (int)$request->service_id;
                $new_appoint_service->cost = $cost->cost;
                $new_appoint_service->save();
            }
            if($request->myserv != "")
            {
                $array = explode(',', $request->myserv);
                if(count($array) > 0)
                {
                    foreach ($array as $key => $value) {
                        $cost_s = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$value)->get()->first();
                        $new_appoint_service = new AppointmentService();
                        $new_appoint_service->appointment_id = $new_appoint->id;
                        $new_appoint_service->service_id = (int)$value;
                        $new_appoint_service->cost = $cost_s->cost;
                        $new_appoint_service->save();
                    }
                }
            }  
            return response()->json(['error' => false,'data' => $new_appoint],200);
        }else{
            $new_appoint = new Appointment();
            $new_appoint->client_id = $request->client_id;
            $new_appoint->pet_id = $request->pet_id;
            $new_appoint->vehicle_id = $request->vehicle;
            $new_appoint->date = $request->date;
            $new_appoint->time_id = $request->time;
            $new_appoint->total_cost = $request->total_cost;
            if($request->payment_mode != null){
                $new_appoint->payment_mode = $request->payment_mode;
            }
            $new_appoint->status = 1;
            $new_appoint->save();
            $cost = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$request->service_id)->get()->first();
            if($cost != null)
            {
                $new_appoint_service = new AppointmentService();
                $new_appoint_service->appointment_id = $new_appoint->id;
                $new_appoint_service->service_id = (int)$request->service_id;
                $new_appoint_service->cost = $cost->cost;
                $new_appoint_service->save();
            }
            if($request->additional_service != "")
            {
                $array = explode(',', $request->additional_service);
                if(count($array) > 0)
                {
                    foreach ($array as $key => $value) {
                        $cost_s = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$value)->get()->first();
                        $new_appoint_service = new AppointmentService();
                        $new_appoint_service->appointment_id = $new_appoint->id;
                        $new_appoint_service->service_id = (int)$value;
                        $new_appoint_service->cost = $cost_s->cost;
                        $new_appoint_service->save();
                    }
                }
            }    
            return response()->json(['error' => false,'data' => $new_appoint],200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    // listing data
    public function show(Appointment $appointment)
    {
        //
        $appointment =Appointment::with('pet.category','pet.coat','pet.breed','pet.aggresive_level','items.service')->where('id','=',$appointment->id)->get()->first();
        return new AppointmentResource($appointment);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function edit(Appointment $appointment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    //update data
    public function update(Request $request, Appointment $appointment)
    {
        //
        $id = (int)$appointment->id;
        $new_appoint = Appointment::find($id);
        // $new_appoint->client_id = $request->client_id;
        // $new_appoint->pet_id = $request->pet_id;
        $new_appoint->vehicle_id = $request->vehicle;
        $new_appoint->date = $request->date;
        $new_appoint->time_id = $request->time;
        $new_appoint->total_cost = $request->total_cost;
        $new_appoint->save();
        $cost = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$request->service_id)->get()->first();
        if($cost != null)
        {
            $new_appoint_service = AppointmentService::find((int)$request->appoint_service);
            // $new_appoint_service->appointment_id = $new_appoint->id;
            $new_appoint_service->service_id = (int)$request->service_id;
            $new_appoint_service->cost = $cost->cost;
            $new_appoint_service->save();
        }
        if($request->additional_service != "")
        {
            $array = explode(',', $request->additional_service);
            if(count($array) > 0)
            {
                $id=[]; 
                array_push($id, (int)$request->appoint_service);
                foreach ($array as $key => $value) {
                    $cost_s = ServiceCost::where('service_mode','=',(int)$request->vehicle)->where('service_id','=',(int)$value)->get()->first();
                    $check = AppointmentService::where('appointment_id','=',$new_appoint->id)->where('service_id','=',(int)$value)->get()->first();
                    if($check == null)
                    {
                        $new_appoint_service = new AppointmentService();
                        $new_appoint_service->appointment_id = $new_appoint->id;
                        $new_appoint_service->service_id = (int)$value;
                        $new_appoint_service->cost = $cost_s->cost;
                        $new_appoint_service->save();
                        array_push($id, $new_appoint_service->id);
                    }
                    else
                    {
                        $check->service_id = (int)$value;
                        $check->cost = $cost_s->cost;
                        $check->save();
                        array_push($id, $check->id);
                    }
                    
                }
            }
            $get_apoint_id = AppointmentService::where('appointment_id','=',$new_appoint->id)->select('id')->get()->toArray();

            $delete = AppointmentService::whereIn('id', $get_apoint_id)->whereNotIn('id', $id)->where('appointment_id','=',$new_appoint->id)->delete();
        }    
        return response()->json(['error' => false,'data' => $new_appoint],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Appointment  $appointment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Appointment $appointment)
    {
        //
    }
    public function appointmentSearch(Request $request)
    {
        //
        // dd($request->all());
        $range = [$request->date_from,$request->date_to];
        $appointment= Appointment::whereBetween('date',$range)->where('status','!=',5)->where('status','!=',6)->get();
        return AppointmentResource::collection($appointment); 
    }
    public function clientappointment(Request $request)
    {
        //
        // dd($request->all());
        
        $appointment= Appointment::where('client_id',(int)$request->client_id)->orderBy('id','desc')->get();
        return AppointmentResource::collection($appointment); 
    }
    public function free_vehicle(Request $request)
    {
        //
        // dd($request->all());
        
        $appointment = Appointment::find((int)$request->id);
        if($appointment != null)
        {
            $client = Client::find($appointment->client_id);
            $vehicle_zone = VehicleZone::select('vehicle_id')->where('zone_id','=',$client->zone_id)->get()->toArray();

            //free according to time and date
            $free_vehicle = Appointment::select('vehicle_assign')->whereDate('date','=',$appointment->date)->where('time_id','=',$appointment->time_id)->get()->toArray();
            // dd($free_vehicle);
            $vehicle = Vehicle::whereIn('id',$vehicle_zone)->whereNotIn('id',$free_vehicle)->get();

            $vehicle= Vehicle::where('vehicle_type',(int)$appointment->vehicle_id)->orderBy('id','desc')->get();
            // $vehicle = Vehicle::with('groomer.user','zone.zone')->get();
            return VehicleResource::collection($vehicle);
        } 
    }
    public function assign_appointment(Request $request)
    {
        //
        // dd($request->all());
        
        $appointment = Appointment::find((int)$request->id);
        if($appointment != null)
        {
            $appointment->vehicle_assign = $request->vehicle_assign;
            $appointment->status =2;
            $appointment->save();
        } 
        return response()->json(['error' => false],200);
    }
    public function schedule_appointment(Request $request)
    {
        //
        // dd($request->all());
        
        $appointment = Appointment::find((int)$request->id);
        if($appointment != null)
        {
            $appointment->date = $request->date;
            $appointment->time_id =$request->time;
            $appointment->save();
        } 
        return response()->json(['error' => false],200);
    }
    public function cancel_appointment(Request $request)
    {
        //
        // dd($request->all());
        
        $appointment = Appointment::find((int)$request->id);
        if($appointment != null)
        {
            $appointment->status =5;
            $appointment->save();
        } 
        $new_appoint = new CancelAppointment();
        $new_appoint->appointment_id = $request->id;
        $new_appoint->user_id = $request->user_id;
        $new_appoint->reason = $request->reason;
        $new_appoint->comment = $request->comment;
        $new_appoint->save();
        return response()->json(['error' => false, 'data' => $new_appoint],200);
    }
    public function upcomingappointment(Request $request)
    {
        // dd(Auth::user());
        $user =User::where('id','=',Auth::id())->get()->first();
        $appointment= Appointment::whereDate('date','>',date('Y-m-d'))->where('status','!=',5)->where('status','!=',6)->orderBy('date','asc')->get();
        return response()->json(['error' => false,'data' => AppointmentResource::collection($appointment),'user' => $user],200);
        // return ;
    }
    public function feedback_appointment(Request $request)
    {
        $pet = Pet::find((int)$request->pet_id);
        if($pet != null)
        {
            $pet->coat_level = $request->coat_level;
            $pet->aggresive = $request->aggressive;
            $pet->save();
        } 

        if($request->feedback != ''){
            $appointment = Appointment::find((int)$request->id);
            $appointment->status =6;
            $appointment->save();
        }

        $appointment_process = ProcessAppointment::where('appointment_id',(int)$request->id)->orderBy('id','desc')->get()->first();
        if($appointment_process != null)
        {
            $appointment_process->appointment_id = $request->id;
            $appointment_process->user_id = $request->user_id;
            $appointment_process->rating = $request->rating;
            $appointment_process->feedback = $request->feedback;
            $appointment_process->save();
            return response()->json(['error' => false],200);
        }else{
            $new_appoint = new ProcessAppointment();
            $new_appoint->appointment_id = $request->id;
            $new_appoint->user_id = $request->user_id;
            $new_appoint->rating = $request->rating;
            $new_appoint->feedback = $request->feedback;
            $new_appoint->save();

            return response()->json(['error' => false, 'data' => $new_appoint],200);
        }
    }
    public function process_appointment(Request $request)
    {
        if(isset($request->total_amount) && $request->total_amount != ''){
            $appointment = Appointment::find((int)$request->id);
            if($appointment != null)
            {
                // $appointment->status =3;
                $appointment->total_cost =$request->total_amount;
                $appointment->save();
            } 
            $appointment_process = ProcessAppointment::where('appointment_id',(int)$request->id)->orderBy('id','desc')->get()->first();
            if($appointment_process != null)
            {
                $appointment_process->appointment_id = $request->id;
                $appointment_process->user_id = $request->user_id;
                $appointment_process->payment_method = $request->payment_method;
                $appointment_process->save();
                return response()->json(['error' => false],200);
            }else{
                $new_appoint = new ProcessAppointment();
                $new_appoint->appointment_id = $request->id;
                $new_appoint->user_id = $request->user_id;
                $new_appoint->payment_method = $request->payment_method;
                $new_appoint->save();

                return response()->json(['error' => false, 'data' => $new_appoint],200);
            }
        }else{
        
            // $appointment = Appointment::find((int)$request->id);
            // if($appointment != null)
            // {
            //     $appointment->status =3;
            //     $appointment->save();
            // } 

            $appointment_process = ProcessAppointment::where('appointment_id',(int)$request->id)->orderBy('id','desc')->get()->first();
            if($appointment_process != null)
            {
                $appointment = Appointment::find((int)$request->id);
                if($appointment != null){
                    $appointment->status =4;
                    $appointment->save();
                }

                $appointment_process->appointment_id = $request->id;
                $appointment_process->user_id = $request->user_id;
                $appointment_process->start_process_date = $request->start_process_date;
                $appointment_process->process_start_time = $request->process_start_time;
                $appointment_process->end_process_date = $request->end_process_date;
                $appointment_process->process_end_time = $request->process_end_time;
                $appointment_process->startprocess_image_files = $request->startprocess_image_files;
                $appointment_process->endprocess_image_files = $request->endprocess_image_files;
                $appointment_process->payment_method = $request->payment_method;
                $appointment_process->save();
                return response()->json(['error' => false],200);
            }else{
                $appointment = Appointment::find((int)$request->id);
                if($appointment != null){
                    $appointment->status =3;
                    $appointment->save();
                }

                $new_appoint = new ProcessAppointment();
                $new_appoint->appointment_id = $request->id;
                $new_appoint->user_id = $request->user_id;
                $new_appoint->start_process_date = $request->start_process_date;
                $new_appoint->process_start_time = $request->process_start_time;
                $new_appoint->end_process_date = $request->end_process_date;
                $new_appoint->process_end_time = $request->process_end_time;
                $new_appoint->startprocess_image_files = $request->startprocess_image_files;
                $new_appoint->endprocess_image_files = $request->endprocess_image_files;
                $new_appoint->payment_method = $request->payment_method;
                $new_appoint->save();

                return response()->json(['error' => false, 'data' => $new_appoint],200);
            }
        }
    }

    public function groomerappointments(Request $request) 
    {
        $user = User::where('id','=',$request->uid)->get()->first();
        $other_user = OtherUser::where('user_id','=',$request->uid)->get()->first();
        $appointment = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            //->join('other_users', 'other_users.id', '=', 'vehicles.groomer_id')
            ->where('vehicles.groomer_id','=', $other_user->id)
            ->where('appointments.date','=', date('Y-m-d'))
            ->where('appointments.status','!=',5)
            ->where('appointments.status','!=',6)
            ->get(['appointments.*']);
        $appointmentservice = AppointmentService::all();
        
        return response()->json(['error' => false,'data' => AppointmentResource::collection($appointment),'user' => $user,'appointmentservice' => $appointmentservice],200);
    }

    public function excelappointments(Request $request) 
    {
        $appointment1_main = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            ->join('other_users', 'other_users.id', '=', 'vehicles.groomer_id')
            ->join('users', 'users.id', '=', 'other_users.user_id')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['appointments.*'])->toArray();

        $appointment1 = Appointment::join('clients', 'clients.id', '=', 'appointments.client_id')
            ->join('users', 'users.id', '=', 'clients.user_id')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['clients.*', 'users.*'])->toArray();

        $appointment2 = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            ->join('other_users', 'other_users.id', '=', 'vehicles.groomer_id')
            ->join('users', 'users.id', '=', 'other_users.user_id')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['other_users.*', 'users.*'])->toArray();

        $appointment3 = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['vehicles.*'])->toArray();

        $appointment4 = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            ->join('pets', 'pets.id','=', 'appointments.pet_id')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['pets.*'])->toArray();

        $appointment5 = Appointment::join('vehicles', 'vehicles.id', '=', 'appointments.vehicle_assign')
            ->join('pets', 'pets.id','=', 'appointments.pet_id')
            ->join('breeds', 'breeds.id','=', 'pets.breed_id')
            ->where('appointments.date','>=', date('Y-m-d'))
            // ->where('appointments.status','!=',5)
            // ->where('appointments.status','!=',6)
            ->get(['breeds.*'])->toArray();

            // var_dump(count($appointment1_main)); die;
        if (count($appointment1_main) > 0) {
        
            for ($i=0; $i < count($appointment1_main); $i++) { 
                $appointment_service = AppointmentService::join('services', 'services.id','=', 'appointment_services.service_id')
                    ->where('appointment_services.appointment_id','=',$appointment1_main[$i]['id'])
                    ->get(['services.*'])->toArray();

                $service_bar = '';
                for ($j=0; $j < count($appointment_service); $j++) { 
                    $service_bar .= $appointment_service[$j]['name'].",";
                }

                // var_dump($service_bar);

                // vehicle_type
                if($appointment3[$i]['vehicle_type'] == 1){
                    $vehicle_type = "Bike";
                }else{
                    $vehicle_type = "Vanity Van";
                }

                // coat_level
                if($appointment4[$i]['coat_level'] == 1){
                    $pet_coat_level = 'Low';
                }elseif($appointment4[$i]['coat_level'] == 2){
                    $pet_coat_level = 'Medium';
                }else{
                    $pet_coat_level = 'High';
                }

                // pet_aggresive
                if($appointment4[$i]['aggresive'] == 1){
                    $pet_aggresive = 'Low';
                }elseif($appointment4[$i]['aggresive'] == 2){
                    $pet_aggresive = 'Medium';
                }else{
                    $pet_aggresive = 'High';
                }

                if($appointment1_main[$i]['status'] == 2){
                    $status = "Assigned";
                }elseif($appointment1_main[$i]['status'] == 3){
                    $status = "Start Process";
                }elseif($appointment1_main[$i]['status'] == 4){
                    $status = "End Process";
                }elseif($appointment1_main[$i]['status'] == 5){
                    $status = "Suspend";
                }elseif($appointment1_main[$i]['status'] == 6){
                    $status = "Complete";
                }else{
                    $status = "Pending";
                }
                
                $arrayName2[] = array('client_name' => $appointment1[$i]['name'], 'client_email' => $appointment1[$i]['email'], 'client_phone' => $appointment1[$i]['phone'], 'client_address' => $appointment1[$i]['address'], 'client_gender' => $appointment1[$i]['gender'], 'client_dob' => $appointment1[$i]['dob'], 'client_alternate_address' => $appointment1[$i]['alternate_address'], 'client_alternate_phone' => $appointment1[$i]['alternate_phone'], 'groomer_name' => $appointment2[$i]['name'], 'groomer_email' => $appointment2[$i]['email'], 'groomer_phone' => $appointment1[$i]['phone'], 'vehicle_type' => $vehicle_type, 'vehicle' => $appointment3[$i]['name'], 'vehicle_number' => $appointment3[$i]['vehicle_number'], 'pet_name' => $appointment4[$i]['name'], 'pet_breed' => $appointment5[$i]['name'], 'pet_dob' => $appointment4[$i]['dob'], 'pet_aggresive' => $pet_aggresive, 'pet_coat_level' => $pet_coat_level, 'services' => $service_bar, 'status' => $status, 'total_cost' => $appointment1_main[$i]['total_cost']);

                $arrayName[] = array('Client Name' => $appointment1[$i]['name'], 'Client Email' => $appointment1[$i]['email'], 'Client Phone' => $appointment1[$i]['phone'], 'Client Address' => $appointment1[$i]['address'], 'Client Gender' => $appointment1[$i]['gender'], 'Client DOB' => $appointment1[$i]['dob'], 'Client Alternate Address' => $appointment1[$i]['alternate_address'], 'Client Alternate Phone' => $appointment1[$i]['alternate_phone'], 'Groomer Name' => $appointment2[$i]['name'], 'Groomer Email' => $appointment2[$i]['email'], 'Groomer Phone' => $appointment1[$i]['phone'], 'Vehicle Type' => $vehicle_type, 'Vehicle Name' => $appointment3[$i]['name'], 'Vehicle Number' => $appointment3[$i]['vehicle_number'], 'Pet Name' => $appointment4[$i]['name'], 'Pet Breed' => $appointment5[$i]['name'], 'Pet DOB' => $appointment4[$i]['dob'], 'Pet Aggresive' => $pet_aggresive, 'Pet Coat Level' => $pet_coat_level, 'Services' => $service_bar, 'Status' => $status, 'Total Cost' => $appointment1_main[$i]['total_cost']);
            }

                    // var_dump($arrayName2); die;
            
            return response()->json(['error' => false,'data' => $arrayName,'data2' => $arrayName2],200);
        }else{
            return response()->json(['error' => false,'data' => '','data2' => ''],200);
        }
    }

    public function check_process(Request $request){
    // $appointments = DB::table('appointments')
    //             ->join('vehicles', function ($join) {
    //                 $join->on('appointments.vehicle_id', '=', 'vehicles.id')
    //                     ->where('appointments.date','=',date('Y-m-d'))
    //                     ->where('appointments.status','!=',5);
    //             })
    //             ->select('appointments.*')
    //             ->get();
    //     return response()->json(['error' => false,'data' => $appointments],200);
        //dd($request->id);
        $c_process = ProcessAppointment::where('appointment_id',(int)$request->id)->orderBy('id','desc')->get();
        return ProcessAppointmentResource::collection($c_process);
    }

    public function upload(Request $request)
    {
        if($request->hasFile('file'))
        {  
            $stuImg = $request->file('file');
            $allowedExtns = array('jpg', 'jpeg', 'png');
            $stuImgName = $stuImg->getClientOriginalName();
            $stuImgExt = $stuImg->getClientOriginalExtension();
            $filename = time() . ".$stuImgExt";
            
            if(in_array($stuImgExt, $allowedExtns)){
                $img = Image::make($stuImg);
                
                $img->save( public_path('uploads/usermain/'.$filename ) );
            }
        }else{
            $filename = '';
        }
        return $filename;
    }

    public function getpetappointment(Request $request)
    {
        return '';
    }
}
