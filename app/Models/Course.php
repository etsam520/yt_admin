<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $table = 'courses';
    protected $fillable = [
        'name',
        'description',
        'slug',
        'image',
        'status',
        'trade_node_id',
    ];
    public $timestamps = true;
    protected $casts = [
        'status' => 'string',
        'trade_node_id' => 'integer',
    ];
    /**
     * Get the trade node associated with the course.
     */
    public function tradeNode()
    {
        return $this->belongsTo(TradeNode::class, 'trade_node_id');
    }
    /**
     * Get the courses for a specific trade node.
     */
    public static function getCoursesByTradeNode($tradeNodeId)
    {
        return self::where('trade_node_id', $tradeNodeId)
            ->orderBy('created_at', 'desc')
            ->get();
    }
    /**
     * Get the course by slug.
     */
    public static function getCourseBySlug($slug)
    {
        return self::where('slug', $slug)->first();
    }
    /**
     * Get the course by ID.
     */
    public static function getCourseById($id)
    {
        return self::find($id);
    }
}
