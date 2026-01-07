<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\VirtualRoom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
class VirtualRoomController extends Controller
{
    

    public function connect(Request $request){
        // Log::info($request->all());
        $mirroryKye = $request->input('mirror_key');
        $room = VirtualRoom::where('room_token', $mirroryKye)->first()->toArray();
        
        if($room == null){
          return apiResponse(false, 'Room not found', [], 404);
        }
        unset($room['created_at']);
        unset($room['updated_at']);
        
        return apiResponse(true, 'Room found', ['room' => $room]);

      
    }

}
                                                                                                