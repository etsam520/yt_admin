<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetPdf extends Model
{
    protected $table = 'set_pdfs';
    protected $fillable = [
        'file_path',
        'url_path',
        'set_id',
    ];
}
