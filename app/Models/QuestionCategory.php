<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
class QuestionCategory extends Model
{
    protected $table = 'question_categories';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'depth_index',
        'parent_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'name'      => 'string',
        'slug'      => 'string',
        'parent_id' => 'integer',
        // If depth_index is meant to be an enum, you can use a custom cast or validation
    ];

    /**
     * Get the parent category.
     */
    public function parent()
    {
        return $this->belongsTo(QuestionCategory::class, 'parent_id')->orderBy('id', 'asc');
    }

    /**
     * Get the child categories.
     */
    public function children()
    {
        return $this->hasMany(QuestionCategory::class, 'parent_id')->orderBy('id', 'desc');
    }
}
