<?php

namespace App\Http\Controllers;

use App\Models\AddTransaction;
use App\Models\WalletBalance;
use App\Models\OpeningClosingTable;
use Illuminate\Http\Request;
use App\Models\walletprocessed;
use App\Models\zonedetails;
use App\Models\billings;
use App\Models\Orders;
use App\Models\line_items;
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

        $openclose_data =DB::table("opening_closing_tables")
              ->where('opening_closing_tables.vid',$request->vid)
              ->orderBy('id','DESC')
              ->limit(1)
              ->get();
                if(count($openclose_data) >= 1){
                $opening_balance=$openclose_data[0]->closing_bal+$request->amount;
                $opening_balance2=$openclose_data[0]->closing_bal-$request->amount;
            }else{
                $opening_balance=$request->amount;
                $opening_balance2=0-$request->amount;
            }

        $wallet_data = new OpeningClosingTable();
        $wallet_data->vid=$request->vid;
        
        if($trans_data->type=='In'){  

            if(count($openclose_data) == 0){
               $wallet_data->opening_bal= $request->amount;
               $wallet_data->closing_bal=$request->amount;
            }else{
                $wallet_data->opening_bal=$openclose_data[0]->closing_bal;
                $wallet_data->closing_bal=$openclose_data[0]->closing_bal+$request->amount;
            }           

            $Wallet_order_data[]=[     
                'date_created'=> $request->date,
                'oid'=>0,
                'vid'=>$request->vid,
                'payment_mode'=>"cash",
                'status'=>"N/A",
                'sale_amount'=>0,
                'Wallet_used'=>0,
                'logistic_cost'=>0,
                'payment_gateway_charges'=>0,
                'sms_cost'=>0,
                'majime_charges'=>0,
                'net_amount'=>$request->amount,
                'current_wallet_bal'=>$opening_balance,
                'order_count'=> 0,
                'zone_amt'=> 0,
                'description'=> $request->description
            ];      
        }else{
            $wallet_data->opening_bal=$openclose_data[0]->closing_bal;
            $wallet_data->closing_bal=$openclose_data[0]->closing_bal-$request->amount;

            $Wallet_order_data[]=[     
                'date_created'=> $request->date,
                'oid'=>0,
                'vid'=>$request->vid,
                'payment_mode'=>"cash",
                'status'=>"N/A",
                'sale_amount'=>0,
                'Wallet_used'=>0,
                'logistic_cost'=>0,
                'payment_gateway_charges'=>0,
                'sms_cost'=>0,
                'majime_charges'=>0,
                'net_amount'=>$request->amount,
                'current_wallet_bal'=>$opening_balance2,
                'order_count'=> 0,
                'zone_amt'=> 0,
                'description'=> $request->description
            ];    
        }

        $wallet_data->save();  
        walletprocessed::insert($Wallet_order_data);
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
    // $data = AddTransaction::all()->orderBy("amount")->get();
      $data = AddTransaction::all()->sortBy('vid');
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
