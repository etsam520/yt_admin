<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuestionTrans extends Model
{
    //
    protected $table = 'question_trans';
    protected $fillable = [
        'question_id',
        'locale',
        'title',
        'A',
        'B',
        'C',
        'D',
        'answer_details',
    ];
    public $timestamps = false;
    protected $casts = [
        'question_id' => 'integer',
        'locale' => 'string',
        'title' => 'string',
        'A' => 'string',
        'B' => 'string',
        'C' => 'string',
        'D' => 'string',
        'answer_details' => 'string',
    ];
}
