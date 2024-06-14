<?php


namespace App\controllers;

use App\exceptions\EntityNotExistException;
use App\exceptions\RequestValidatorException;
use Doctrine\ORM\EntityManager;
use App\model\ValidatorFactory;
use App\repositories\ColorsRepository;
use Color;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use App\validators\ColorValidator;

class ColorController {
    private ColorsRepository $colorsRepository;
    function __construct(
        private EntityManager $em,
        private ValidatorFactory $validatorFactory
    ){
        $this->colorsRepository = $em->getRepository(Color::class);
    }

    function getColors( ServerRequest $req ,Response $res , $args) {
        $colors = $this->colorsRepository->getAll();
        return $res->withJson($colors);
    }

    function createColor(ServerRequest $req ,Response $res , $args) {
        try {
            $data = $req->getParsedBody();
            $this->validatorFactory->make(ColorValidator::class)->validate($data);
            $color = $this->colorsRepository->createColor($data["name"],$data["hex-code"]);
            $res = $res->withJson([
                "message" => "color created successfully with id : " . $color->getId() 
            ]);
        }catch (RequestValidatorException $e ) {
            $res = $res->withJson($e,400);
        }
        return $res;
    }
    

    function updateColor(ServerRequest $req ,Response $res , $args) {
        try {
            $data = $req->getQueryParams();
            $id = $args["id"];
            $this->validatorFactory->make(ColorValidator::class)->validate($data);
            $color = $this->colorsRepository->updateColor((int) $id,$data["name"],$data["hex-code"]);
            $res = $res->withJson([
                "message" => "color updated successfully with id : " . $color->getId() 
            ]);
        }catch (RequestValidatorException $e ) {
            $res = $res->withJson($e,400);
        }catch (EntityNotExistException $e) {
            $res = $res->withJson(["message"=>$e->getMessage()],400);
        }
        return $res;
    }

    function deleteColor(ServerRequest $req ,Response $res , $args) {
        $id = (int) $args["id"];
        try {
            $this->colorsRepository->deleteColorById($id);
            $res = $res->withJson([
                "message" => "color with id $id deleted successfully"
            ]);
        } catch (EntityNotExistException $e) {
            $res= $res->withJson([
                "message" => $e->getMessage()
            ],400);
        }

        return $res;
    }


}