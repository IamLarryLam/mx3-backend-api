require('./bootstrap');

import Papa from 'papaparse';

const msRest = require("@azure/ms-rest-js");
const Face = require("@azure/cognitiveservices-face");
const uuid = require("uuid/v4");


let key = "cf421e82604340f59cb64eaec8cb1aa1";
let endpoint = "https://vuillermoz.cognitiveservices.azure.com/";
let prefixURL = "";
let faceListUUID = "mx3_persistant_faces";
let imageUrl = '';
let idBand = '';
let url = '';

Papa.parse('../data/images.csv', {
    download: true,
    delimiter: ",",
    header: false,
    newline: "\r\n",

    // OPTIONNAL PARAMETERS 
    // skipEmptyLines: false, //other option is 'greedy', meaning skip delimiters, quotes, and whitespace.
    // quotes: false, //or array of booleans
    // quoteChar: '',
    // escapeChar: '',
    // columns: null, //or array of strings



    complete: function (results) {
        results.data.forEach(band => {
            console.log(band);

            idBand = band[0];
            imageUrl = band[1];

            url = endpoint + "/face/v1.0/largefacelists/" + faceListUUID + "/persistedfaces?userData=" + idBand + "&detectionModel=detection_03"
            fetch("" + url + "", {
                method: "POST",
                headers: new Headers({
                    "Content-Type": "application/json",
                    "Host": "" + endpoint + "",
                    "Ocp-Apim-Subscription-Key": "" + key + ""
                }),
                body: JSON.stringify({
                    "url": "" + imageUrl + ""
                })
            });
            console.log(url);
        });

        setTimeout(() => {
            fetch("" + endpoint + "face/v1.0/largefacelists/" + faceListUUID + "/train", {
                method: "POST",
                headers: new Headers({
                    "Content-Type": "application/json",
                    "Host": "" + endpoint + "",
                    "Ocp-Apim-Subscription-Key": "" + key + ""
                }),
            })
        }, 500);
    }

});

