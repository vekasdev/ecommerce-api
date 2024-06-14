<?php

 
return [
    "base-path" => realpath(__DIR__."/../.."),
    "imageUploadConfig" => [
        "storageDirectory" => realpath(__DIR__."/../../public/images"),
        "acceptedExtensions" => ['image/jpeg','image/png'],
        "maxSize" => 2097152
    ],
    "gmail-smtp-config" => [
        "email" => "hassanalsadi6@gmail.com",
        "password" => "uucu lhey vxda jtwi"
    ],
    "captcha-code-expire" => (new DateTime("+5 minutes"))->getTimestamp() // five minutes
];

