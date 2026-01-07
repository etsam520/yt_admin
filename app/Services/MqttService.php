<?php

namespace App\Services;

use PhpMqtt\Client\MqttClient;
use Illuminate\Support\Facades\Log;
use PhpMqtt\Client\Facades\MQTT;

class MqttService
{
    protected $mqtt;

    // public function __construct(MqttClient $mqtt)
    // {
    //     $this->mqtt = $mqtt;
    // }

    public function publish($topic, $message, $qos = 0, $retain = false)
    {
        try {
            MQTT::publish($topic, $message);
            return true;
        } catch (\Exception $e) {
            Log::error('MQTT Publish failed: ' . $e->getMessage());
            return false;
        }
    }

    public function subscribe($topic, callable $callback, $qos = 0)
    {
        try {
            $this->mqtt->subscribe($topic, $callback, $qos);
            return true;
        } catch (\Exception $e) {
            Log::error('MQTT Subscribe failed: ' . $e->getMessage());
            return false;
        }
    }

    public function disconnect()
    {
        try {
            $this->mqtt->disconnect();
            Log::info('MQTT Client disconnected');
        } catch (\Exception $e) {
            Log::error('MQTT Disconnect failed: ' . $e->getMessage());
        }
    }
}