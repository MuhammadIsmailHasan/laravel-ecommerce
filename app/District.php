<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function customer()
    {
        return $this->hasMany(Customer::class);
    }

    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
