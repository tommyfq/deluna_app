<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'ms_category';
    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const TYPE = [
        'category',
        'subcategory'
    ];
}
