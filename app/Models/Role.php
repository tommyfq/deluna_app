<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'ms_role';
    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
