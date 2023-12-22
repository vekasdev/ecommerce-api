<?php


namespace App\controllers;

use Doctrine\ORM\EntityManager;
use App\model\ValidatorFactory;
use App\repositories\ColorsRepository;
use Color;
use Slim\Http\Response;
use Slim\Http\ServerRequest;

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
}