<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UploadedFile extends Model
{
    protected $fillable = [
        'original_name',
        'stored_name',
        'path',
        'mime_type',
        'size',
        'question_id' // Optional: for relating to questions
    ];
}
