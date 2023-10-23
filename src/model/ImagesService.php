<?php


namespace App\model;

use App\dtos\UploadedImage;
use App\exceptions\UploadedFileException;
use Slim\Psr7\UploadedFile;

class ImagesService {
    public function __construct(
        private string $storageDir,
        private array $acceptedExtensions =["image/jpeg","image/png"],
        private int $maxSize = 2097152
    ) {}

    function validate(UploadedFile $uploadedFile){
        if($uploadedFile->getError() == UPLOAD_ERR_OK){
            if($uploadedFile->getSize() > $this->maxSize) {
                throw new UploadedFileException(
                    "you can not exceed the size of : " . $this->maxSize . "bytes"
                );
            }
            if(!in_array($uploadedFile->getClientMediaType(),
                $this->acceptedExtensions)) 
            {
                throw new UploadedFileException(
                    "file extension not accepted"
                );
            }
        }
        return true;
    }

    function save(UploadedFile $uploadedFile)  : UploadedImage{
        $this->validate($uploadedFile);
        $fileName = $this->generateRandomName();
        $extension = $this->getExtensionFromMediaType($uploadedFile->getClientMediaType());
        $uploadedFile->moveTo(
            $this->storageDir.
            "/".
            $fileName.".".$extension);
        return new UploadedImage($fileName,$extension);
    }

    private function generateRandomName(){
        return uniqid("image");
    }

    private function getExtensionFromMediaType(string $mediaType){
        $arr = explode("/",$mediaType);
        $mediaType = end($arr);
        return  $mediaType;
    }

    function getStorageDir(){
        return $this->storageDir;
    }

    /**
     * @var $fqfn full qualified file name
     * @return string image binary
     * @return bool when not exist
     */
    function getImage($fqfn):string|bool {
        $image = file_get_contents($this->storageDir."/".$fqfn);
        if(!$image)return false;
        return $image;
    }
}