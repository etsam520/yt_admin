<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PptTbl extends Model
{
    //
    protected $table = 'ppt_tbls';
    protected $fillable = [
        'en_path',
        'hi_path',
        'u_path',
        'is_public',
        'is_converted',
    ];
    public $timestamps = true;
    protected $casts = [
        'en_path' => 'string',
        'hi_path' => 'string',
        'u_path' => 'string',
        'is_public' => 'boolean',
        'is_converted' => 'boolean',
    ];
    /**
     * Get the PPT by ID.
     */
    public static function getPptById($id)
    {
        return self::find($id);
    }
    /**
     * Get all PPTs.
     */
    public static function getAllPpts()
    {
        return self::orderBy('created_at', 'desc')->get();
    }
    /**
     * Get PPTs by public status.
     */
    public static function getPptsByPublicStatus($isPublic)
    {
        return self::where('is_public', $isPublic)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by conversion status.
     */
    public static function getPptsByConversionStatus($isConverted)
    {
        return self::where('is_converted', $isConverted)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by language.
     */
    public static function getPptsByLanguage($language)
    {
        return self::where('en_path', 'like', "%{$language}%")
            ->orWhere('hi_path', 'like', "%{$language}%")
            ->orWhere('u_path', 'like', "%{$language}%")
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by path.
     */
    public static function getPptsByPath($path)
    {
        return self::where('en_path', $path)
            ->orWhere('hi_path', $path)
            ->orWhere('u_path', $path)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get PPTs by public status and conversion status.
     */
    public static function getPptsByPublicAndConversionStatus($isPublic, $isConverted)
    {
        return self::where('is_public', $isPublic)
            ->where('is_converted', $isConverted)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by public status and language.
     */
    public static function getPptsByPublicAndLanguage($isPublic, $language)
    {
        return self::where('is_public', $isPublic)
            ->where(function ($query) use ($language) {
                $query->where('en_path', 'like', "%{$language}%")
                    ->orWhere('hi_path', 'like', "%{$language}%")
                    ->orWhere('u_path', 'like', "%{$language}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by conversion status and language.
     */
    public static function getPptsByConversionAndLanguage($isConverted, $language)
    {
        return self::where('is_converted', $isConverted)
            ->where(function ($query) use ($language) {
                $query->where('en_path', 'like', "%{$language}%")
                    ->orWhere('hi_path', 'like', "%{$language}%")
                    ->orWhere('u_path', 'like', "%{$language}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by public status, conversion status, and language.
     */
    public static function getPptsByPublicConversionAndLanguage($isPublic, $isConverted, $language)
    {
        return self::where('is_public', $isPublic)
            ->where('is_converted', $isConverted)
            ->where(function ($query) use ($language) {
                $query->where('en_path', 'like', "%{$language}%")
                    ->orWhere('hi_path', 'like', "%{$language}%")
                    ->orWhere('u_path', 'like', "%{$language}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get PPTs by public status and path.
     */
    public static function getPptsByPublicAndPath($isPublic, $path)
    {
        return self::where('is_public', $isPublic)
            ->where(function ($query) use ($path) {
                $query->where('en_path', $path)
                    ->orWhere('hi_path', $path)
                    ->orWhere('u_path', $path);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
