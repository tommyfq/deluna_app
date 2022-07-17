<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OptionType extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'ms_option_types';
    protected $fillable = [
        'name',
        'is_active',
        'created_by',
        'updated_by',
        'deleted_by',
    ];
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function option()
    {
        return $this->hasMany(Option::class, 'option_type_id', 'id');
    }
}
