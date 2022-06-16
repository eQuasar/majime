<?php

namespace App\Http\Controllers;

use App\Models\AddTransaction;
use Illuminate\Http\Request;

class AddTransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          $data = AddTransaction::all();
        return $data;
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
                // 'id' => 'required',
                'description' => 'required',
                'type' => 'required',
                'amount' => 'required',
                'date' => 'required',
             ]);
            
            
            $trans_data = new AddTransaction();
            
            $trans_data->id= $request->id;
            $trans_data->vid= $request->vid;
            $trans_data->description=$request->description;
            $trans_data->type=$request->type;
            $trans_data->amount=$request->amount;
            $trans_data->date=$request->date;
        
            $trans_data->save();

       		if($trans_data->type=='In')
       		{
       			 $wallet_data = new walletbalance ();
       			 $wallet_data->id=$request->id;
       			 $wallet_data->uid=$request->uid;
       			 $wallet_data->oid=$request->oid;
       			 $wallet_data->vid=$request->vid;
       			 $wallet_data->opening_bal=$request->opening_bal;
       			 $wallet_data->closing_bal=$request->closing_bal;
       			 $wallet_data->save();
       		}
       		else
       		{
       			echo "hello";
       		}


            return response()->json(['error' => false,'data' => $trans_data],200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AddTransaction  $addTransaction
     * @return \Illuminate\Http\Response
     */
    public function show(AddTransaction $addTransaction)
    {
        //
$data = AddTransaction::all();
        return $data;

        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AddTransaction  $addTransaction
     * @return \Illuminate\Http\Response
     */
    public function edit(AddTransaction $addTransaction)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AddTransaction  $addTransaction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AddTransaction $addTransaction)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AddTransaction  $addTransaction
     * @return \Illuminate\Http\Response
     */
    public function destroy(AddTransaction $addTransaction)
    {
        //
    }
}
