<?php

namespace App\Http\Controllers;

use App\Services\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MqttController extends Controller
{
    protected $mqttService;

    // public function __construct(MqttService $mqttService)
    // {
    //     $this->mqttService = $mqttService;
    // }

    public function publish(Request $request)
    {
        // return 'ok';
        // $request->validate([
        //     'topic' => 'required|string',
        //     'message' => 'required|string',
        //     'qos' => 'sometimes|integer|min:0|max:2',
        // ]);

        $topic = "World";
        $message = $request->message ?? 'Hello World';
        $qos = 0;
        $mqttService = new MqttService();
        $success = $mqttService->publish($topic, $message, $qos);
        // $success = $this->mqttService->publish(
        //     $request->topic,
        //     $request->message,
        //     $request->qos ?? 0
        // );

        return response()->json([
            'success' => $success,
            'message' => $success ? 'Message published' : 'Failed to publish message'
        ]);
    }

    public function getStatus(): JsonResponse
    {
        return response()->json([
            'status' => 'connected',
            'server' => config('mqtt.server'),
            'port' => config('mqtt.port')
        ]);
    }
}