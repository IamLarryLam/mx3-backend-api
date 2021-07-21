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

//License
Config::set('endpoint', 'https://vuillermoz.cognitiveservices.azure.com/');
Config::set('key', 'cf421e82604340f59cb64eaec8cb1aa1');

Config::set('maxNumberOfReturnedCandidates', 15);

// Optionnal (for admin methods)
Config::set('prefixURL', 'https://i.imgur.com/'); //Server link where images of uploads are stored. For example : https://mx3.ch/public/images/. IMPORTANT end with a /

//Do not change
Config::set('faceListUUID', 'mx3_persistant_faces'); //Mx3 list ID


//LARAVEL ROUTING 

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api')->group(function () {

    //PUBLIC 
    Route::get('findSimilar/{image_url}', [apiController::class, 'findSimilarFromImageUrl']);

    // ADMIN
    Route::get('addArtistsFromCSV', [apiController::class, 'addArtistsFromCSV']);
    Route::get('refreshArtistList', [apiController::class, 'refreshArtistList']);

    Route::get('addArtistFromImageUrl/{image_url}/{idBand}', [apiController::class, 'addArtistFromImageUrl']); //prefixURL need to be configured

    Route::get('createArtistList', [apiController::class, 'createArtistList']); //Only do once (ALREADY DONE)
    Route::get('deleteArtistList', [apiController::class, 'deleteArtistList']); //Attention this will delete the list and will need to be recreated

    // For testing
    Route::get('callAPI', [apiController::class, 'callAPI']);
    Route::get('detectFace/{image_url}', [apiController::class, 'detectFaceJs']);
    Route::get('getArtistIdFromUUID/{faceId}', [apiController::class, 'getArtistIdFromUUID']);
});