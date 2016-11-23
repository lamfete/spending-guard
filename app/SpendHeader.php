<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use App\SpendHeader as Header;
//use App\SpendDetail as Detail;

class SpendHeader extends Model
{
    public function details() {
    	return $this->hasMany('App\SpendDetail');
    }
}
