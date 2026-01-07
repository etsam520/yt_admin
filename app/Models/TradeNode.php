<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeNode extends Model
{
    //
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
    ];
    protected $casts = [
        'parent_id' => 'integer',
    ];
    public function parent()
    {
        return $this->belongsTo(TradeNode::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(TradeNode::class, 'parent_id');
    }
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    public function scopeWithChildren($query)
    {
        return $query->with('children');
    }
    public function scopeWithParent($query)
    {
        return $query->with('parent');
    }
    public function scopeWithAll($query)
    {
        return $query->with(['children', 'parent']);
    }
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
