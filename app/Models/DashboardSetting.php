<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DashboardSetting extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'value_type',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function getSetting($userId, $key)
    {
        $setting = self::where('user_id', $userId)->where('key', $key)->first();
        return $setting ? self::castValue($setting->value, $setting->value_type) : null;
    }

    public static function setSetting(int $userId,string $key, $value, string $valueType = 'string')
    {
        $setting = self::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value, 'value_type' => $valueType]
        );

        return $setting;
    }

    public static function deleteSetting($userId, $key)
    {
        return self::where('user_id', $userId)->where('key', $key)->delete();
    }

    public static function getAllSettings($userId)
    {
        return self::where('user_id', $userId)->get()->mapWithKeys(function ($item) {
            return [$item->key => self::castValue($item->value, $item->value_type)];
        });
    }


    private static function castValue($value, $valueType)
    {
        switch ($valueType) {
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN);
            case 'array':
                return json_decode($value, true);
            case 'string':
            default:
                return $value;
        }
    }


}
