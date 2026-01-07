<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DashboardSetting;
use Illuminate\Http\Request;

class DashboardSettingController extends Controller
{
    const DEFAULT_VALUS = [
        'questions' => [
            "type" => "array",
            "value" => []
        ],
        'language' => [
            "type" => "string",
            "value" => "en"
        ],
        'general' => null
    ];


    public function getAllSettings(Request $request)
    {
        $userId = $request->user()->id;
        $settings = \App\Models\DashboardSetting::getAllSettings($userId);
        return apiResponse(200, 'success', $settings, 200);
    }

    public function getSetting(Request $request, $key)
    {
        $userId = $request->user()->id;
        $setting = \App\Models\DashboardSetting::getSetting($userId, $key);
        // if($setting == null)
            // $setting = \App\Models\DashboardSetting::setSetting(userId: $userId, key: $key, value: self::DEFAULT_VALUS[$key]);
        return apiResponse(200, 'success', $setting, 200);
    }

    public function setSetting(Request $request)
    {
        $userId = $request->user()->id;
        $key = $request->input('key');
        $value = $request->input('value');
        $oldData = $this->getSavedDate($key);
        // dd($key, $oldData, $value);
        $payload =  $this->getSavePayloadWithKey($oldData, $key, $value);
        $setting = DashboardSetting::setSetting(userId: $payload['user_id'], key: $payload['key'], value: json_encode($payload['data']), valueType: $payload['type']);
        return apiResponse(200, 'success', $setting, 200);
    }

    public function deleteSetting(Request $request, $key)
    {
        $userId = $request->user()->id;
        \App\Models\DashboardSetting::deleteSetting($userId, $key);
        return apiResponse(200, 'success', null, 200);
    }

    private function getSavedDate($key) : array {
        $keys = explode('.', $key);
        $data = [];
        if(count($keys) != 0)
            $data = DashboardSetting::getSetting(auth()->user()->id, $keys[0]);
        return $data ?? [];
    }

    private function getSavePayloadWithKey(array $data, string $key, $value) : array
    { 
        $keys = explode('.', $key);
        $firstKey = $keys[0];

        $dataType = "string";

        if (!isset($data[$firstKey])) {
            $data[$firstKey] = [];
        }

        if (count($keys) > 1) {
            // remove first key from path
            array_shift($keys);
            $nestedKey = implode('.', $keys);

            // IMPORTANT FIX
            $data[$firstKey] = $this->setNestedValue($data[$firstKey], $nestedKey, $value);

            $dataType = "array";

        } else {
            $data[$firstKey] = $value;
            $dataType = gettype($value);
        }

        return [
            'key' => $firstKey,
            'data' => $data, 
            'type' => $dataType, 
            'user_id' => auth()->user()->id
        ];
    }


    private function setNestedValue(array $array, string $path, $value): array
    {  
        $ref = &$array;   // keep reference to root array
        $keys = explode('.', $path);

        foreach ($keys as $key) {
            if (!isset($ref[$key]) || !is_array($ref[$key])) {
                $ref[$key] = [];
            }
            $ref = &$ref[$key];
        }

        $ref = $value;

        return $array;
    }
}