<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Symfony\Component\HttpKernel\Exception\HttpException;

class apiController extends Controller
{
    public function addArtistsFromCSV()
    {
        // return view('importcsv');

        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $file = public_path('data/images.csv');

        $arrayArtist = $this->csvToArray($file);

        foreach ($arrayArtist as $artist) {
            $idBand = $artist['id'];
            $imageUrl = $artist['url'];

            $url = "$endpoint/face/v1.0/largefacelists/$faceListUUID/persistedfaces?userData=$idBand&detectionModel=detection_03";
            Http::withoutVerifying()->withHeaders([
                'Content-Type' => 'application/json',
                'Ocp-Apim-Subscription-Key' => $key
            ])->post("$url", [
                'url' => $imageUrl,
            ]);
        }
        Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->post("$endpoint/face/v1.0/largefacelists/$faceListUUID/train");
        return response('Done importing the csv file', 200);
    }

    function csvToArray($filename = '', $delimiter = ',')
    {
        if (!file_exists($filename) || !is_readable($filename))
            return false;
        $header = null;
        $data = array();
        if (($handle = fopen($filename, 'r')) !== false) {
            while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
                if (!$header)
                    $header = $row;
                else
                    $data[] = array_combine($header, $row);
            }
            fclose($handle);
        }
        return $data;
    }

    public function callAPI()
    {
        echo '<script>
        fetch("http://127.0.0.1:8000/api/refreshArtistList", {
            method: "GET"
        }).then(result => console.log(result.status))</script>';
    }

    public function getArtistIdFromUUID($faceUuid)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $url = "$endpoint/face/v1.0/largefacelists/$faceListUUID/persistedfaces/$faceUuid";
        $request = 'fetch("' . $url . '", {
            method: "GET",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            })
        })';
        echo ('<script>' . $request . '
            .then(res => res.json())
            .then(console.log);</script>');
    }

    public function addArtistFromImageUrl($image_url, $idBand)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');
        $faceListUUID = Config::get('faceListUUID');
        $url = "$endpoint/face/v1.0/largefacelists/$faceListUUID/persistedfaces?userData=$idBand&detectionModel=detection_03";

        $request = Http::async()->withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->post("$url", [
            'url' => $prefixURL . $image_url,
        ])->wait(Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->post("$endpoint/face/v1.0/largefacelists/$faceListUUID/train"));

        try {
            if ($request != null) {
                return response('successs blabla', 200);
            }
            return $request->getBody();
        } catch (HttpException $ex) {
            return $ex;
        }
    }

    public function refreshArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->post("$endpoint/face/v1.0/largefacelists/$faceListUUID/train");
    }
    
    public function findSimilarFromImageUrl($image_url)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');
        $faceListUUID = Config::get('faceListUUID');
        $maxNumberOfReturnedCandidates = Config::get('maxNumberOfReturnedCandidates');

        $request = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->post("$endpoint/face/v1.0/detect?detectionModel=detection_03&recognitionModel=recognition_03", [
            'url' => $prefixURL . $image_url
        ]);
        if ($request != null) {
            if (isset($request[1])) {
                return response('Error, too many faces', 409);
            } else {

                $request2 = Http::withoutVerifying()->withHeaders([
                    'Content-Type' => 'application/json',
                    'Ocp-Apim-Subscription-Key' => $key
                ])->post("$endpoint/face/v1.0/findsimilars", [
                    "faceId" => $request[0]['faceId'],
                    "largeFaceListId" => "$faceListUUID",
                    "maxNumOfCandidatesReturned" => $maxNumberOfReturnedCandidates,
                    "mode" => "matchFace"
                ]);

                try {
                    return $request2->getBody();
                } catch (HttpException $ex) {
                    return $ex;
                }
            }
        }
    }

    public function createArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $request = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->put("$endpoint/face/v1.0/largefacelists/$faceListUUID", [
            'name' => $faceListUUID,
            'userData' => $faceListUUID,
            'recognitionModel' => 'recognition_03',
        ]);

        try {
            return $request->getBody();
        } catch (HttpException $ex) {
            return $ex;
        }
    }

    public function deleteArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $request = Http::withoutVerifying()->withHeaders([
            'Content-Type' => 'application/json',
            'Ocp-Apim-Subscription-Key' => $key
        ])->delete("$endpoint/face/v1.0/largefacelists/$faceListUUID");

        try {
            return $request->getBody();
        } catch (HttpException $ex) {
            return $ex;
        }
    }
}