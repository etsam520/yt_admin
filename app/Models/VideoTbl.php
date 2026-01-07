<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoTbl extends Model
{
    //
    protected $table = 'video_tbls';
    protected $fillable = [
        'title',
        'description',
        'video_url',
        'is_public',
    ];
    public $timestamps = true;
    protected $casts = [
        'title' => 'string',
        'description' => 'string',
        'video_url' => 'string',
        'is_public' => 'boolean',
    ];
    /**
     * Get the video by ID.
     */
    public static function getVideoById($id)
    {
        return self::find($id);
    }
    /**
     * Get all public videos.
     */
    public static function getPublicVideos()
    {
        return self::where('is_public', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get all videos.
     */
    public static function getAllVideos()
    {
        return self::orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get videos by title.
     */
    public static function getVideosByTitle($title)
    {
        return self::where('title', 'like', '%' . $title . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get videos by public status.
     */
    public static function getVideosByPublicStatus($isPublic)
    {
        return self::where('is_public', $isPublic)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get videos by description.
     */
    public static function getVideosByDescription($description)
    {
        return self::where('description', 'like', '%' . $description . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get videos by URL.
     */
    public static function getVideosByUrl($videoUrl)
    {
        return self::where('video_url', 'like', '%' . $videoUrl . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get videos by multiple criteria.
     */
    public static function getVideosByCriteria($criteria)
    {
        $query = self::query();

        if (isset($criteria['title'])) {
            $query->where('title', 'like', '%' . $criteria['title'] . '%');
        }
        if (isset($criteria['description'])) {
            $query->where('description', 'like', '%' . $criteria['description'] . '%');
        }
        if (isset($criteria['video_url'])) {
            $query->where('video_url', 'like', '%' . $criteria['video_url'] . '%');
        }
        if (isset($criteria['is_public'])) {
            $query->where('is_public', $criteria['is_public']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }
    /**
     * Get the latest video.
     */
    public static function getLatestVideo()
    {
        return self::orderBy('created_at', 'desc')->first();
    }
    /**
     * Get the count of all videos.
     */
    public static function getVideoCount()
    {
        return self::count();
    }
    /**
     * Get the count of public videos.
     */
    public static function getPublicVideoCount()
    {
        return self::where('is_public', true)->count();
    }
    /**
     * Get the count of private videos.
     */
    public static function getPrivateVideoCount()
    {
        return self::where('is_public', false)->count();
    }
    /**
     * Get the count of videos by title.
     */
    public static function getVideoCountByTitle($title)
    {
        return self::where('title', 'like', '%' . $title . '%')->count();
    }
    /**
     * Get the count of videos by description.
     */
    public static function getVideoCountByDescription($description)
    {
        return self::where('description', 'like', '%' . $description . '%')->count();
    }
    /**
     * Get the count of videos by URL.
     */
    public static function getVideoCountByUrl($videoUrl)
    {
        return self::where('video_url', 'like', '%' . $videoUrl . '%')->count();
    }
    /**
     * Get the count of videos by public status.
     */
    public static function getVideoCountByPublicStatus($isPublic)
    {
        return self::where('is_public', $isPublic)->count();
    }
}
