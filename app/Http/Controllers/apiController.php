<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;

class apiController extends Controller
{
    public function addArtistsFromCSV()
    {
        return view('importcsv');
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
        // Larry's artist face UUID : e1fe8857-975e-471a-8dfd-9dbe891dae8c
        // Ophelie's artist face UUID : 45b04866-6446-4733-b0ee-88f598e06806
        // Larry's face TEMPORAIRE 24h : 70d962c7-1248-4a8e-840e-d02387ddb664
        // FaceList UUID : mx3_persistant_faces
        // cf421e82604340f59cb64eaec8cb1aa1
        // vuillermoz

        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');
        $faceListUUID = Config::get('faceListUUID');
        $url = "$endpoint/face/v1.0/largefacelists/$faceListUUID/persistedfaces?userData=$idBand&detectionModel=detection_03";

        $request = 'fetch("' . $url . '", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "url": "' . $prefixURL . $image_url . '"
            })
        })';
        echo ('<script>' . $request . '
            .then(res => res.json())
            .then(console.log);</script>');

        echo ('<script>setTimeout(() => { fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $faceListUUID . '/train", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            })
        }) }, 500);
        </script>');
    }

    public function refreshArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        echo '<script>
                fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $faceListUUID . '/train", {
                    method: "POST",
                    headers: new Headers({
                        "Content-Type": "application/json",
                        "Host": "' . $endpoint . '",
                        "Ocp-Apim-Subscription-Key": "' . $key . '"
                    })
                }).then(response => console.log(response.status))</script>';
    }
    public function findSimilarFromImageUrl($image_url)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');
        $faceListUUID = Config::get('faceListUUID');
        $maxNumberOfReturnedCandidates = Config::get('maxNumberOfReturnedCandidates');

        $request = 'fetch("' . $endpoint . 'face/v1.0/detect?detectionModel=detection_03&recognitionModel=recognition_03", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "url": "' . $prefixURL . $image_url . '"
            })
        })';
        echo ('<script>' . $request . '.then(res => res.json())
        .then(face => {
            console.log(face[0].faceId);
            if (face[1] != null) {
                return { "error": "Too many faces" }
            } else {
                return fetch("' . $endpoint . 'face/v1.0/findsimilars", {
                    method: "POST",
                    headers: new Headers({
                        "Content-Type": "application/json",
                        "Host": "' . $endpoint . '",
                        "Ocp-Apim-Subscription-Key": "' . $key . '"
                    }),
                    body: JSON.stringify({
                        "faceId": face[0].faceId,
                        "largeFaceListId": "' . $faceListUUID . '",
                        "maxNumOfCandidatesReturned": ' . $maxNumberOfReturnedCandidates . ' ,
                        "mode": "matchFace"
                    })
                })
                }
        }).then(res => {
            if(res["error"]==null)
            {
                return res.json();
            }else{
                return res;
            }
        })
        .then(console.log);</script>');
    }

    public function detectFaceJs($image_url)
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $prefixURL = Config::get('prefixURL');

        $request = 'fetch("' . $endpoint . 'face/v1.0/detect?detectionModel=detection_03&recognitionModel=recognition_03", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "url": "' . $prefixURL . $image_url . '"
            })
        })';
        echo ('<script>' . $request . '.then(res => res.json())
        .then(face=>console.log(face[0].faceId));</script>');
    }

    public function createArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $request = 'fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $faceListUUID . '", {
            method: "PUT",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            }),
            body: JSON.stringify({
                "name": "large-face-list-name",
                "userData": "User-provided data attached to the large face list.",
                "recognitionModel": "recognition_03"
            })
        });';

        $request2 = 'setTimeout(() => { fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $faceListUUID . '/train", {
            method: "POST",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            })
        }) }, 500);';
        echo ('<script>' . $request . $request2  . '</script>');
    }

    public function deleteArtistList()
    {
        $key = Config::get('key');
        $endpoint = Config::get('endpoint');
        $faceListUUID = Config::get('faceListUUID');

        $request = 'fetch("' . $endpoint . 'face/v1.0/largefacelists/' . $faceListUUID . '", {
            method: "DELETE",
            headers: new Headers({
                "Content-Type": "application/json",
                "Host": "' . $endpoint . '",
                "Ocp-Apim-Subscription-Key": "' . $key . '"
            })
        }).then(console.log)';

        echo ('<script>' . $request   . '</script>');
    }
}



// CURL
// public function detectFaceGuzzle($image_url)
// {
//     //VARIABLES
//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $prefixURL = Config::get('prefixURL');
//     $image_url = $prefixURL . $image_url;
//     $url = $endpoint . 'face/v1.0/detect';
//     //END VARIABLES

//     $response = Http::withHeaders([
//         'Content-Type' => 'application/json',
//         'Host' => "$endpoint",
//         'Ocp-Apim-Subscription-Key' => "$key"
//     ])->post($url, [
//         'url' => $image_url
//     ]);

//     var_dump($response);
// }

// public function detectFaceLaravel($image_url)
// {
//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $prefixURL = Config::get('prefixURL');
//     $image_url = $prefixURL . $image_url;
//     $url = $endpoint . 'face/v1.0/detect';

//     $response = $this->postJson($url, ['url' => $image_url]);

//     $response
//         ->assertStatus(201)
//         ->assertJson([
//             'created' => true,
//         ]);
// }
// public function detectFace3($image_url)
// {
//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $prefixURL = Config::get('prefixURL');

//     $curl = curl_init($endpoint . 'face/v1.0/detect');

//     $headers = [
//         "Content-Type: application/json",
//         "Host: $endpoint",
//         "Ocp-Apim-Subscription-Key: ' . $key . '"
//     ];

//     // set post fields
//     $post = [
//         'url' => $prefixURL . $image_url,
//     ];

//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($curl, CURLOPT_POSTFIELDS, $post);

//     // execute!
//     $response = curl_exec($curl);

//     // close the connection, release resources used
//     curl_close($curl);

//     // do anything you want with your response
//     var_dump($response);
// }


// public function detectFace($image_url)
// {
//     $prefixURL = Config::get('prefixURL');
//     $url = $prefixURL . $image_url;

//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $options = array(
//         'http' => array(
//             'header'  =>  "Content-Type: application/json\r\nHost: $endpoint\r\nOcp-Apim-Subscription-Key: ' . $key . '\r\n",
//             'method'  => 'POST',
//             'content' => "url=$url"
//         )
//     );
//     $result = file_get_contents($endpoint . 'face/v1.0/detect', false, stream_context_create($options));
// }
// public function detectFace5($image_url)
// {
//     $method = 'POST';
//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $prefixURL = Config::get('prefixURL');
//     $url = $prefixURL . $image_url;
//     $headers = [
//         "Content-Type: application/json",
//         "Host: $endpoint",
//         "Ocp-Apim-Subscription-Key: ' . $key . '"
//     ];
//     $data = json_encode([
//         'url' => $prefixURL . $image_url,
//     ]);

//     $curl = curl_init();
//     switch ($method) {
//         case "POST":
//             curl_setopt($curl, CURLOPT_POST, 1);
//             if ($data)
//                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//             break;
//         case "PUT":
//             curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
//             if ($data)
//                 curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
//             break;
//         default:
//             // if ($data)
//             // $url = sprintf("%s?%s", $url, http_build_query($data));
//     }
//     // OPTIONS:
//     curl_setopt($curl, CURLOPT_URL, $url);
//     curl_setopt($curl, CURLOPT_HTTPHEADER, array(
//         "Content-Type: application/json",
//         "Host: $endpoint",
//         "Ocp-Apim-Subscription-Key: ' . $key . '"
//     ));
//     curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//     curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
//     // EXECUTE:
//     $result = curl_exec($curl);
//     if (!$result) {
//         die("Connection Failure");
//     }
//     curl_close($curl);
//     return $result;
// }

// public function detectFace4($image_url)
// {
//     $key = Config::get('key');
//     $endpoint = Config::get('endpoint');
//     $prefixURL = Config::get('prefixURL');

//     $headers = [
//         "Content-Type: application/json",
//         "Host: $endpoint",
//         "Ocp-Apim-Subscription-Key: ' . $key . '"
//     ];
//     $post = [
//         'url' => $prefixURL . $image_url,
//     ];

//     $processed = FALSE;
//     $ERROR_MESSAGE = '';

//     // ************* Call API:
//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $endpoint . 'face/v1.0/detect');

//     curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
//     curl_setopt($ch, CURLOPT_POST, 1); // set post data to true
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $post);   // post data
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     curl_setopt($ch, CURLOPT_VERBOSE, true);
//     curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);


//     $json = curl_exec($ch);
//     curl_exec($ch);

//     $info = curl_getinfo($ch);
//     // print_r($info['request_header']);
//     echo curl_getinfo($ch, CURLINFO_HEADER_OUT);
//     curl_close($ch);
//     // returned json string will look like this: {"code":1,"data":"OK"}
//     // "code" may contain an error code and "data" may contain error string instead of "OK"
//     // $obj = json_decode($json);

//     // if ($obj->{'code'} == '1') {
//     //     $processed = TRUE;
//     // } else {
//     //     $ERROR_MESSAGE = $obj->{'data'};
//     // }


//     // if (!$processed && $ERROR_MESSAGE != '') {
//     //     echo $ERROR_MESSAGE;
//     // }
// }