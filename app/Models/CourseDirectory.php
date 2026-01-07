<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseDirectory extends Model
{
    protected $table = 'course_directories';
    protected $fillable = [
        'name',
        'course_id',
        'parent_id',
        'status',
    ];
    public $timestamps = false;
    protected $casts = [
        'course_id' => 'integer',
        'parent_id' => 'integer',
    ];
    /**
     * Get the course associated with the directory.
     */
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
    /**
     * Get the parent directory if it exists.
     */
    public function parent()
    {
        return $this->belongsTo(CourseDirectory::class, 'parent_id');
    }
    /**
     * Get the child directories.
     */
    public function children()
    {
        return $this->hasMany(CourseDirectory::class, 'parent_id');
    }
    /**
     * Get all directories for a specific course.
     */
    public static function getDirectoriesByCourse($courseId)
    {
        return self::where('course_id', $courseId)
            ->orderBy('name')
            ->get();
    }
    /**
     * Get a directory by its ID.
     */
    public static function getDirectoryById($id)
    {
        return self::find($id);
    }
}
