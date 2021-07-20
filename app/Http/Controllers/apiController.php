<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class apiController extends Controller
{
    public function test()
    {
        echo ('yess');
    }

    public function detectFace($image_url)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');

        $request = 'fetch("' . $endpoint . 'face/v1.0/detect", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "vuillermoz.cognitiveservices.azure.com",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "url": "' . $prefixURL . $image_url . '"
            })
        })';
        // echo ('<script>' . $request . '</script>');
        echo ('<script>' . $request . '.then(res => res.json())
        .then(console.log);</script>');
    }

    public function createPersistantFaceList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $largeFaceListId = 'mx3_persistant_faces';

        $request = 'fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $largeFaceListId . '", {
            method: "PUT",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "vuillermoz.cognitiveservices.azure.com",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "name": "large-face-list-name",
                "userData": "User-provided data attached to the large face list.",
                "recognitionModel": "recognition_04"
            })
        })';
        echo ('<script>' . $request . '</script>');
        // echo ('<script>' . $request . '.then(function(response) {console.log(response.status);});</script>');
    }
}