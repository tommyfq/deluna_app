<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tr_log';
    protected $fillable = [
        'reference_id',
        'type',
        'stock_from',
        'stock_to',
        'created_by',
    ];
    
    const CREATED_AT = 'created_at';
}
