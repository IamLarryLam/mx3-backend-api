<?php

use App\Http\Controllers\apiController;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Config::set('endpoint', 'https://vuillermoz.cognitiveservices.azure.com/');
Config::set('key', 'cf421e82604340f59cb64eaec8cb1aa1');
Config::set('prefixURL', 'https://i.imgur.com/');
Config::set('tempFileURL', 'UASY9gJ.jpg');
Config::set('faceListUUID', 'mx3_persistant_faces');
Config::set('maxNumberOfReturnedCandidates', 15);


Route::get('/', function () {
    return view('welcome');
});


Route::prefix('api')->group(function () {

    Route::get('addFace', [apiController::class, 'test']);
    Route::get('detectFace/{image_url}', [apiController::class, 'detectFaceJs']);
    Route::get('getArtistIdFromUUID/{faceId}', [apiController::class, 'getArtistIdFromUUID']);
    Route::get('createPersistantFaceList', [apiController::class, 'createPersistantFaceList']);
    Route::get('addArtistFromImageUrl/{image_url}', [apiController::class, 'addArtistFromImageUrl']);
    Route::get('findSimilar/{image_url}', [apiController::class, 'findSimilarFromImageUrl']);
    Route::get('addPersistantFaceFromImageUrl/{image_url}/{idBand?}', [apiController::class, 'addPersistantFaceFromImageUrl']);
});