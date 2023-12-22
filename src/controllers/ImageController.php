<?php 

namespace App\controllers;


use App\repositories\MainCategoriesRepository;
use App\dtos\EntryPersisted;
use App\exceptions\EntityNotExistException;
use App\exceptions\RequestValidatorException;
use App\model\ImagesService;
use App\model\ValidatorFactory;
use App\repositories\CategoriesRepository;
use App\repositories\ImagesRepository;
use App\validators\AddCategoryValidator;
use App\validators\CreateMainCategoryValidator;
use Category;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Image;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;
use MainCategory;

class ImageController {

    private ImagesRepository $imagesRepository;
    function __construct(
        private EntityManager $entityManager,
        private ValidatorFactory $validatorFactory,
        private ImagesService $imagesService

    ) {
        $this->imagesRepository = $this->entityManager->getRepository(Image::class);
    }

    function getImage(ServerRequest $req, Response $res, array $args) {
        try { 
            $name = $req->getParams()["name"];

            $image = $this->imagesRepository->getImage($name);

            $imageBinary = $this->imagesService->getImage($image->getFullFileName());

            $res->getBody()->write($imageBinary);
            $res = $res->withHeader("Content-Type",$image->getMediaType());
            
        } catch (RequestValidatorException $e) { 
            $res = $res->withJson($e,400);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson([
                "status" => "failure",
                "message" => $e->getMessage(),
        ],400);
        } catch (NoResultException $e) {
            $res = $res->withJson(["error"=>"image not exist"],400);
        }

        return $res;
    }
}