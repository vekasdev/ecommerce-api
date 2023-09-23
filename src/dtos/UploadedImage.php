<?php
namespace App\dtos;
class UploadedImage{
    function __construct(
        public string $fileName,
        public string $fileExtention
    ){}

}