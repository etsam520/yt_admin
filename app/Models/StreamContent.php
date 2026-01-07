<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamContent extends Model
{
    protected $table = 'stream_contents';
    protected $fillable = [
        'type',
        'target_table',
        'target_id',
        'course_directory_id',
        'trade_node_id',
        'question_sets_id' // not required in normal use cases, but can be used for specific scenarios
    ];
    public $timestamps = true;
    protected $casts = [
        'type' => 'string',
        'target_table' => 'string',
        'target_id' => 'integer',
        'course_directory_id' => 'integer',
    ];
    /**
     * Get the course directory associated with the stream content.
     */
    public function courseDirectory()
    {
        return $this->belongsTo(CourseDirectories::class, 'course_directory_id');
    }
    /**
     * Get the target model instance based on the target table and ID.
     */
    public function target()
    {
        return $this->morphTo(null, 'target_table', 'target_id');
    }
    /**
     * Get all stream contents for a specific course directory.
     */
    public static function getStreamContentsByDirectory($directoryId)
    {
        return self::where('course_directory_id', $directoryId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get a stream content by its ID.
     */
    public static function getStreamContentById($id)
    {
        return self::find($id);
    }
    /**
     * Get stream contents by type.
     */
    public static function getStreamContentsByType($type)
    {
        return self::where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get stream contents by target table and ID.
     */
    public static function getStreamContentsByTarget($targetTable, $targetId)
    {
        return self::where('target_table', $targetTable)
            ->where('target_id', $targetId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get stream contents by course directory and type.
     */
    public static function getStreamContentsByDirectoryAndType($directoryId, $type)
    {
        return self::where('course_directory_id', $directoryId)
            ->where('type', $type)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
