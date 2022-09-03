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
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
        'deleted_at'
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    const STATUS_CREATED = 'created';
    const STATUS_WAITING_FOR_PAYMENT = 'waiting_for_payment';
    const STATUS_PAID = 'paid';
    const STATUS_CANCELED = 'canceled';
    const STATUS_FINISHED = 'finished';
    const STATUS_ARRAY = [
        'created' => 1,
        'waiting_for_payment' => 2,
        'paid' => 3,
        'canceled' => 4,
        'finished' => 5
    ];

    public function sales(){
        return $this->belongsTo(SalesChannel::class,'sales_channel_id','id');
    }

    public function order_detail(){
        return $this->hasMany(OrderDetail::class,'order_header_id','id');
    }

    public function order_log(){
        return $this->hasMany(OrderLog::class,'order_header_id','id');
    }
}
