<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SpendHeader as Header;

class SpendDetail extends Model
{
    public function header() {
    	return $this->belongsTo('Header');
    }
}
