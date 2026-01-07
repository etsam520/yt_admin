<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionMeta extends Model
{
    

    protected $fillable = [
        'question_id',
        'meta_key',
        'meta_value',
    ];

    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo(QuestionTbl::class, 'question_id');
    }
}
