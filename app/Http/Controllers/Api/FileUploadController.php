<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UploadedFile;
use Faker\Provider\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FileUploadController extends Controller
{
    /*public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // 5MB max
        ]);

        $file = $request->file('file');
        $path = $file->store('public/media');
        
        return response()->json([
            'path' => Storage::url($path),
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }*/
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120', // 5MB max
        ]);

        $fileUploader = FileUploadController::fileUploader($request , 'file');
        if($fileUploader){
            return apiResponse(200 , 'File uploaded successfully' , $fileUploader);
        }
        return apiResponse(500 , 'File upload failed');
       
    }

    public static function fileUploader(Request $request , string $requestFileName) : UploadedFile {
        try {
           
            $file = $request->file($requestFileName);
            if(!$file){
                throw new \Exception('File not found'); 
            }
            $originalName = $file->getClientOriginalName();
            $filesize = $file->getSize();
            $mimeType = $file->getMimeType();
            
            // Generate unique filename with timestamp
            $uniqueFilename = time().rand(1000,9999) . '.' . $file->getClientOriginalExtension();
            
            // Store file in storage/app/public/uploads
            $file->move(public_path('uploads/all'), $uniqueFilename);
            
            // Save file info to database
             $uploadedFile = \App\Models\UploadedFile::create([
                'original_name' => $originalName,
                'stored_name' => $uniqueFilename,
                'path' => 'uploads/all/' . $uniqueFilename,
                'mime_type' => $mimeType,
                'size' => $filesize,
            ]);
            return $uploadedFile;
        } catch (\Throwable $th) {

            Log::error("FILE_UPLOAD_ERROR: ".$th->getMessage());
            return (object) null;
        }

    }

    public function delete(Request $request,int $id)
    {
        $ticker = (bool) self::fileRemover($id);
        if($ticker){
            return response()->json(['message' => 'File deleted successfully']);
        }else{
            return response()->json(['message' => 'File delete failed']);
        }
    }

    public static function fileRemover($id) : bool{
        try {
            $file = UploadedFile::findOrFail($id);
            // Delete from storage
            Storage::delete(public_path('/uploads/' . $file->stored_name));
            // Delete database record
            $file->delete();
            return true;
        } catch (\Throwable $th) {
            Log::error("FILE_DELETE_ERROR: ".$th->getMessage());
            return false;
        }
    }
}


/* backup uploader
public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,pdf|max:5120', // 5MB max
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filesize = $file->getSize();
        $mimeType = $file->getMimeType();
        
        // Generate unique filename with timestamp
        $uniqueFilename = time() . '.' . $file->getClientOriginalExtension();
        
        // Store file in storage/app/public/uploads
        $file->move(public_path('uploads/all'), $uniqueFilename);
        
        // Save file info to database
        $uploadedFile = \App\Models\UploadedFile::create([
            'original_name' => $originalName,
            'stored_name' => $uniqueFilename,
            'path' => 'uploads/all/' . $uniqueFilename,
            'mime_type' => $mimeType,
            'size' => $filesize,
        ]);
      
        return response()->json([
            'message' => 'File uploaded successfully',
            'data' => [
                'id' => $uploadedFile->id,
                'path' => asset('uploads/all/' . $uniqueFilename),
                'file_path' => 'uploads/all/' . $uniqueFilename,
                'original_name' => $originalName,
                'unique_name' => $uniqueFilename,
                'size' => $filesize,
                'mime_type' => $mimeType,
            ],
        ]);
    } */
