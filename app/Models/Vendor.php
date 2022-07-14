<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $table = 'ms_vendors';
    protected $fillable = [
        'slug',
        'name',
        'description',
        'address',
        'phone',
        'status',
        'created_by',
        'updated_by'
    ];

    public $timestamps = true;
}
