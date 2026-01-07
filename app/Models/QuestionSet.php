<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSet extends Model
{
 
    // protected static $table = 'question_sets';
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_public',
        'password',
        'created_by',
        'category_id',
        'category_depth_index',
        'organization_id'
    ];

    
    public $timestamps = true;
    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'created_by' => 'integer',
        'category_id' => 'integer',
        // 'category_depth_index' => 'integer',
        'organization_id' => 'integer',

    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function meta()
    {
        return $this->hasMany(QuestionSetMeta::class, 'question_set_id');
    }

    public function folders()
    {
        return $this->belongsToMany(Folder::class, 'folder_question_set', 'question_set_id', 'folder_id');
    }
}
