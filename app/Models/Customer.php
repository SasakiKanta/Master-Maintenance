<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $guarded = [
        'id', 
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    protected $hidden = [
        'created_at', 
        'updated_at', 
        'deleted_at'
    ];

    public function supplier()
    {
        return $this->belongsTo(\App\Models\Supplier::class);
    }

}
