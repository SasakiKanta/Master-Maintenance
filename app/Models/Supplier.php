<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $guarded = ['id', 'created_at', 'updated_at'];

    protected $hidden = ['created_at'];

}
