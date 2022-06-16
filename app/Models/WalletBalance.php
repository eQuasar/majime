<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WalletBalance extends Model
{
  protected $table = 'walletbalance';
	public $timestamps = true;
		protected $fillable = [
	'id',
	'uid',
	'oid',
	'vid',	
	'opening_bal', 
	'closing_bal',
	
	    
	];
}
