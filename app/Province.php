<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    public function city() 
    {
        return $this->hasMany(City::class);
    }
}
