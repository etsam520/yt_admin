<?php

namespace App\Http\Controllers\Api\Teacher;

use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMeta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function index(Request $request)
    {
        // $user = User::with(['meta' => function ($query) {
        //     $query->leftJoin('uploaded_files', function ($join) {
        //         $join->on('uploaded_files.id', '=', 'user_metas.value')
        //             ->whereIn('user_metas.key', ['avatar', 'coverPhoto']);
        //     })
        //     ->select(
        //         'user_metas.*',
        //         DB::raw("CASE 
        //                     WHEN user_metas.key = 'avatar' THEN uploaded_files.path 
        //                     WHEN user_metas.key = 'coverPhoto' THEN uploaded_files.path 
        //                     ELSE NULL 
        //                 END AS file_path")
        //     );
        // }])->find($request->user()->id);
        $user = User::find($request->user()->id);

        $metas = DB::table('user_metas')
            ->leftJoin('uploaded_files', function ($join) {
                $join->on('uploaded_files.id', '=', 'user_metas.value')
                    ->whereRaw("user_metas.key IN ('avatar', 'cover_photo')");
            })
            ->where('user_metas.user_id', $user->id)
            ->select(
                'user_metas.*',
                DB::raw("CASE 
                            WHEN user_metas.key = 'avatar' THEN uploaded_files.path 
                            WHEN user_metas.key = 'cover_photo' THEN uploaded_files.path 
                            ELSE NULL 
                        END AS file_path")
            )
            ->get();

        $user->meta = $metas;

        return apiResponse(200 , 'success' , $user, 200);
    }

    public function update(Request $request)
    {
        // return response()->json($request->only('social'));
        $validator = Validator::make($request->all(), [
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'title' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'education' => 'nullable|string|max:255',
            'experience' => 'nullable|integer|min:0|max:100',
            'memberSince' => ['nullable', 'date', 'date_format:Y-m-d'],

            'specialization' => ['required', 'array', 'min:1'],
            'specialization.*' => ['string', 'max:255'],

            'social' => 'nullable|array',
            'social.linkedin' => 'nullable|string|max:255',
            'social.twitter' => 'nullable|string|max:255',
            'social.website' => 'nullable|url|max:255',

            
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'coverPhoto' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'available' => 'required|in:0,1,true,false',
        ]);
        // return $request->all();
  
        
        if($validator->fails()){
            return response()->json(['error' => $validator->errors()], 422);
        }
        if(!$request->user()){
            return response()->json(['error' => 'User not found'], 404);
        }
        // return apiResponse( true , 'success' , $request->user('avatar'));
        $validated = $validator->validated();
        apiResponse( true , 'success' , $request->file('avatar'));
        $uploads = [];
        $uplodedavatar = null;
        $uplodedcoverphoto = null;
        $validated['avatar'] =  $validated['coverPhoto']  =  null;
        if($request->hasFile('avatar')) {
          $uplodedavatar =  FileUploadController::fileUploader($request , 'avatar');
            $uploads['avatar'] = $uplodedavatar;
            $validated['avatar'] = $uplodedavatar['id']??null;
        }
        if($request->hasFile('coverPhoto')) {
          $uplodedcoverphoto =  FileUploadController::fileUploader($request , 'coverPhoto');
            $uploads['coverPhoto'] = $uplodedcoverphoto;
            $validated['coverPhoto'] = $uplodedcoverphoto['id']??null;
        }
        // dd('dfdjkfk');
        return $this->saveRequest($validated);

    }


    private function saveRequest($validated, $uploads = []) {
        try {
            DB::beginTransaction();
            $user = auth()->user();
           
            
            $user->update([
                'name' => $validated['firstName'] . ' ' . $validated['lastName'] ?? $user->name,
                'email' => $validated['email'] ?? $user->email,
                'phone' => $validated['phone'] ?? $user->phone,
                'image' => $validated['avatar'] ?? $user->image,
            ]);
         
            if($validated['avatar'] != null){
                $avatarMeta = UserMeta::where('user_id', $user->id)->where('key', 'avatar')->first();
                if($avatarMeta?->value != $validated['avatar'] && $avatarMeta?->value != null){
                    FileUploadController::fileRemover($avatarMeta->value);
                }
            }
            if($validated['coverPhoto'] != null){
                $coverPhotoMeta = UserMeta::where('user_id', $user->id)->where('key', 'cover_photo')->first();
                if($coverPhotoMeta?->value != $validated['coverPhoto'] && $coverPhotoMeta?->value != null){
                    FileUploadController::fileRemover($coverPhotoMeta->value);
                }
            }

            $uId = $user->id;

            $metaFields = [
                'avatar'          => 'avatar',
                'cover_photo'     => 'coverPhoto',
                'firstName'       => 'firstName',
                'profession_title'=> 'title',
                'organization'    => 'organization',
                'bio'             => 'bio',
                'education'       => 'education',
                'experience'      => 'experience',
                'member_since'    => 'memberSince',
                'specialization'  => 'specialization',
                'social'          => 'social',
                'available'       => 'available',
            ];
            Log::info("Meta Fields: " . json_encode($metaFields));

            foreach ($metaFields as $metaKey => $fieldKey) {
                if(config('app.debug')):
                Log::info("field Key : " . $fieldKey);
                Log::info("meta  Key : " . $metaKey);
                if(isset($validated[$fieldKey])) Log::info("meta Value : " . json_encode($validated[$fieldKey]));

                Log::info("meta arrayexists : " . array_key_exists($fieldKey, $validated));
                endif;

                if (array_key_exists($fieldKey, $validated) && $validated[$fieldKey] !== null) {
                    $value = $validated[$fieldKey];

                    // Encode if needed
                    switch ($metaKey) {
                        case 'social':
                            $value = is_array($value) ? json_encode($value) : null;
                            break;
                        case 'specialization':
                            $value = is_array($value) ? json_encode($value) : null;
                            break;  
                        case 'avatar':
                            $avatar = UserMeta::where(['user_id' => $uId, 'key' => 'avatar'])->first();
                            if($avatar?->value) FileUploadController::fileRemover($avatar->value);
                            break;
                        case 'coverPhoto':
                            $coverPhoto = UserMeta::where(['user_id' => $uId, 'key' => 'coverPhoto'])->first();
                            if($coverPhoto?->value) FileUploadController::fileRemover($coverPhoto->value);
                            break;
                        default:
                            break;
                    }
                    
                    
                   $isUpdated = UserMeta::updateOrCreate(['user_id' => $uId, 'key' => $metaKey],['value' => $value]);

                if(config('app.debug')):
                    Log::info("Meta Updated/Created: " . json_encode($isUpdated));
                endif;
                }
            }


            DB::commit();
            return response()->json([
                'message' => 'Profile updated successfully',
                'user' => $user
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            foreach ($uploads as $upload) {
                FileUploadController::fileRemover($upload['id']);
            }
            Log::error('Failed to update profile: ' . $e->getMessage(). $e->getLine(). $e->getFile());
            return response()->json(['error' => 'Failed to update profile'], 500);
        }

    }
}
