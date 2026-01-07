<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\VirtualRoomController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\MqttController;
use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
Route::get('/test', function () {
    // return response()->json(['message' => 'CORS is working!']);
    broadcast(new \App\Events\TestMessage("Hello from Laravel + Socket.IO!"));
    return "Sent!";
});
Route::prefix('mqtt')->group(function () {
    Route::get('/publish', [MqttController::class, 'publish']);
    Route::get('/status', [MqttController::class, 'getStatus']);
});
Route::group(['prefix' => 'samples'], function () {
    Route::get('/sample-question-import', function () {
        return response()->download(public_path('samples/sample-question.docx'));
    });
});

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth routes
Route::controller(AuthController::class)->group(function () {
    Route::post('/login', 'login');
    // Route::post('/register', 'register');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
    Route::get('permissions', 'getMyPermissions')->middleware('auth:sanctum');
});

Route::controller(VirtualRoomController::class)->prefix('mirror')->group(function () {
    Route::post('/connect', 'connect');
});


//Admin
Route::group(['middleware' => 'auth:sanctum', 'prefix' => 'admin'], function () {
    Route::resource('users', 'App\Http\Controllers\Api\Admin\UserController');
    Route::resource('trade-nodes', 'App\Http\Controllers\Api\Admin\TradeNodeController');

    Route::apiResource('organizations', 'App\Http\Controllers\Api\Admin\OrganizationController');
    Route::apiResource('categories', 'App\Http\Controllers\Api\Admin\CategoryController')->only(['index', 'store', 'update']);
    Route::get('/category-by-depth-index', 'App\Http\Controllers\Api\Admin\CategoryController@getCategoryesByDepthIndex'); // For tree structure




    Route::resource('courses', 'App\Http\Controllers\Api\Admin\CourseController');
    Route::get('courses/{course_id}/directories', 'App\Http\Controllers\Api\Admin\CourseDirectory@index');
    Route::resource('directory', 'App\Http\Controllers\Api\Admin\CourseDirectory')->only(['index', 'store', 'update']);
    Route::get('{dir}/{dir_id}/files', 'App\Http\Controllers\Api\Admin\FileController@index');
    Route::resource('files', 'App\Http\Controllers\Api\Admin\FileController')->except(['create', 'store']);
    Route::apiResource('questions', 'App\Http\Controllers\Api\Admin\QuestionController');
    // Route::post('questions/teacher','App\Http\Controllers\Api\Admin\ChapterController@questionsByTeacher');
    Route::post('questions/bulk-import', 'App\Http\Controllers\Api\Admin\QuestionController@bulkImport');
    Route::post('question-bank', 'App\Http\Controllers\Api\Admin\QuestionBankController@index');
    
    // PDF Generation routes
    Route::post('pdf/generate-questions', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@generatePdf');
    Route::get('pdf/list', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@setPdfsList');
    Route::get('pdf/show/{id}', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@showsetPdfById')->name('set-pdf.show');
    Route::delete('pdf/delete/{id}', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@deleteSetPdf');
    // Route::post('pdf/preview', 'App\Http\Controllers\Api\Admin\PdfGeneratorController@previewPdf');
    // Route::get('pdf/templates', 'App\Http\Controllers\Api\Admin\PdfGeneratorController@getTemplates');
    // Route::get('pdf/test-preview', 'App\Http\Controllers\Api\Admin\PdfGeneratorController@previewPdf');
    
    // TCPDF-based PDF Generation routes
    Route::post('tcpdf/generate', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@generatePdf');
    Route::get('tcpdf/test', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@testGeneration');
    Route::apiResource('question-sets', 'App\Http\Controllers\Api\Admin\SetController');
    Route::post('questionsAvalableForSet', 'App\Http\Controllers\Api\Admin\SetController@questionsAvalableForSet');
    Route::get('questionsOfSet/{setId}', 'App\Http\Controllers\Api\Admin\SetController@questionsOfSet');
    Route::post('questionsStoreToSet/{setId}', 'App\Http\Controllers\Api\Admin\SetController@questionsStoreToSet');
    Route::post('question-sets/bulk-action', 'App\Http\Controllers\Api\Admin\SetController@bulkAction');
    Route::delete('question-sets/{setId}/questions', 'App\Http\Controllers\Api\Admin\SetController@removeQuestionsFromSet');
    Route::apiResource('folders', 'App\Http\Controllers\Api\Admin\FolderController')->only(['store', 'update', 'destroy']);
    Route::get('set-folder', 'App\Http\Controllers\Api\Admin\FolderController@setFolder');
    Route::get('set-folder/current/{id}', 'App\Http\Controllers\Api\Admin\FolderController@currentFolder');

    Route::controller('App\Http\Controllers\Api\Admin\DigitalBoardController')->prefix('digital-board')->group(function () {
        Route::get('questions/{setId}', 'questions');
        Route::post('create-mirror', 'createMirror');
        Route::get('mirror-key/{setId}', 'getMirrorKey');
        Route::post('mirror-the-question', 'mirrorTheQuestion');
    });


    Route::group(['prefix' => 'teacher'], function () {
        Route::post('profile', 'App\Http\Controllers\Api\Teacher\ProfileController@update');
        Route::get('profile', 'App\Http\Controllers\Api\Teacher\ProfileController@index');
    });

    Route::group(['prefix' => 'dashboard-settings'], function () {
        Route::get('/', 'App\Http\Controllers\Api\DashboardSettingController@getAllSettings');
        Route::post('/', 'App\Http\Controllers\Api\DashboardSettingController@setSetting');
        Route::get('/{key}', 'App\Http\Controllers\Api\DashboardSettingController@getSetting');
        Route::delete('/{key}', 'App\Http\Controllers\Api\DashboardSettingController@deleteSetting');
    });
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('upload', 'App\Http\Controllers\Api\FileUploadController@upload');
    Route::delete('upload/{id}', 'App\Http\Controllers\Api\FileUploadController@delete');
});

Route::get('test-p', 'App\Http\Controllers\Api\Admin\TcpdfPdfGeneratorController@testGeneratePdf');



