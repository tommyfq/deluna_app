<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tr_order_detail';
    protected $fillable = [
        'order_header_id',
        'product_id',
        'product_option_id',
        'quantity',
        'price',
        'notes',
        'created_at',
        'updated_at',
        'created_by',
        'updated_by'
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function sales(){
        $this->belongsTo(SalesChannel::class);
    }
}
