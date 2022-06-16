<?php

namespace App\Http\Controllers;

use App\Models\AddTransaction;
use App\Models\WalletBalance;
use App\Models\OpeningClosingTable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                   $openclose_data =DB::table("opening_closing_tables")
                  				->orderBy('id','DESC')
                  				->limit(1)
                  				->get();
                     if(!empty($openclose_data))
                    {
               			$wallet_data = new OpeningClosingTable();
			   	       	 $wallet_data->vid=$request->vid;
    			   			 $wallet_data->opening_bal= $request->amount;
    			   			 $wallet_data->closing_bal=$request->amount;
    			   			 $wallet_data->save();  

                    } 
                      else 
                    {
                    
                    	$wallet_data = new OpeningClosingTable();
		       	       	$wallet_data->vid=$request->vid;
		       			$wallet_data->opening_bal =$openclose_data[0]->closing_bal;
		       			$wallet_data->closing_bal= $openclose_data[0]->closing_bal+$request->amount;
		       			$wallet_data->save();
                    }           
            }
          else
          {
          		echo "hello";

          }


                       	     

       			 // $wallet_data = new OpeningClosingTable();
       	   //     	 $wallet_data->vid=$request->vid;
       			 // $t_amunt  = $request->amount;
       			 // $wallet_data->closing_bal=$request->amount;
       			 // $wallet_data->save();
           //  }else{
           //      $opening_data = new OpeningClosingTable();
           //      $opening_data->vid=$request->vid;
           //      $opening_data->opening_bal=$wallet_data->opening_bal+$request->amount;
           //      $opening_data->closing_bal=$opening_data->opening_bal;
           //      $opening_data->save();
                
            


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
