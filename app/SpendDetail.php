<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\SpendHeader as Header;
//use App\SpendDetail as Detail;

class SpendDetail extends Model
{
    public function header() {
    	return $this->belongsTo('App\SpendHeader');
    }
}
