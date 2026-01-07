<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $table = 'organizations';

    protected $fillable = [
        'name',
        'slug',
        'logo',
        'created_by',
        'parent_id',
    ];

    public $timestamps = true;

    protected $casts = [
        'name' => 'string',
        'slug' => 'string',
        'created_by' => 'integer',
    ];

    /**
     * Get the parent organization.
     */
}
