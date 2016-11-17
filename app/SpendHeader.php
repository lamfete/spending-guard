<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SpendDetail as Detail;

class SpendHeader extends Model
{
    public function details() {
    	return $this->hasMany('Detail');
    }
}
