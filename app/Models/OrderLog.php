<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tr_order_log';
    protected $fillable = [
        'order_header_id',
        'order_no',
        'status',
        'created_at',
        'created_by'
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function user(){
        return $this->belongsTo(User::class,'created_by','id');
    }

}
