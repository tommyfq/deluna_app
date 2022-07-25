<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tr_order_header';
    protected $fillable = [
        'order_no',
        'status',
        'total_price',
        'discount',
        'grand_total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address',
        'sales_channel_id',
        'sales_channel_notes',
        'notes',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function sales(){
        $this->belongsTo(SalesChannel::class);
    }
}
