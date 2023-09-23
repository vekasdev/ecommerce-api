<?php

 
return [
    "base-path" => realpath(__DIR__."/../.."),
    "imageUploadConfig" => [
        "storageDirectory" => realpath(__DIR__."/../../public/images"),
        "acceptedExtensions" => ['image/jpeg','image/png'],
        "maxSize" => 2097152
    ],
];

