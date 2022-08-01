<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoleMapping extends Model
{
    use SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'ms_role_mapping';
    protected $fillable = [
        'role_id',
        'menu_id',
        'action_id',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
}
