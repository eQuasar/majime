<?php

namespace App\Http\Controllers;

use App\Models\TimeSlot;
use App\Models\Appointment;
use Illuminate\Http\Request;
use App\Http\Resources\TimeSlotResource;

class TimeSlotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $service_mode= TimeSlot::all();
        return TimeSlotResource::collection($service_mode);
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
        if($request->type == 'get_active')
        {
            // $free_vehicle = Appointment::select('time_id')->whereDate('date','=',$request->date)->get()->toArray();
            // $service_mode= TimeSlot::whereNotIn('id',$free_vehicle)->get();
            $service_mode= TimeSlot::all();
            return TimeSlotResource::collection($service_mode);
        }
        else
        {
            $request->validate([
                 'start_time' => 'required',
                 'end_time' => 'required',
            ]);
            
            
            $new_service = new TimeSlot();
            
            $new_service->start_time = $request->start_time;
            $new_service->end_time = $request->end_time;
            
            $new_service->save();
            return response()->json(['error' => false,'data' => $new_service],200);
        }
            
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function show(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function edit(TimeSlot $timeSlot)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TimeSlot $timeslot)
    {
        $request->validate([
             'start_time' => 'required',
             'end_time' => 'required',
             
        ]);
            
            
            $new_breed = TimeSlot::find((int)$timeslot->id);
            
            $new_breed->start_time = $request->start_time;
            $new_breed->end_time = $request->end_time;
            
            $new_breed->save();
            return response()->json(['error' => false,'data' => $new_breed],200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TimeSlot  $timeSlot
     * @return \Illuminate\Http\Response
     */
    public function destroy(TimeSlot $timeSlot)
    {
        //
    }
}
