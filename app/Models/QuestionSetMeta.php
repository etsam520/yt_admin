<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionSetMeta extends Model
{
    protected $table = 'question_set_metas';
    protected $fillable = [
        'question_set_id',
        'meta_key',
        'meta_value',
    ];
    public $timestamps = false;
    protected $casts = [
        'meta_key'   => 'string',
        'meta_value' => 'string',
    ];

    /**
     * Get the question set that owns the meta.
     */
    public function questionSet()
    {
        return $this->belongsTo(QuestionSet::class, 'question_set_id');
    }
}
