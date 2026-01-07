<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Folder extends Model
{
    protected $fillable = ['name', 'path', 'level', 'parent_id', 'part_of', 'created_by'];

    public function parent()
    {
        return $this->belongsTo(Folder::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Folder::class, 'parent_id');
    }

    public function questionSets()
    {
        return $this->belongsToMany(QuestionSet::class, 'folder_question_set', 'folder_id', 'question_set_id');
    }
    
   
}
