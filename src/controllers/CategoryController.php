<?php


namespace App\controllers;

use App\dtos\EntryPersisted;
use App\repositories\CategoriesRepository;
use Category;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;

class CategoryController {
    private CategoriesRepository $categoriesRepository;
    function __construct(
        private EntityManager $entityManager,
        private ResponseManager $responseManager
    ) {
        $this->categoriesRepository = $entityManager->getRepository(Category::class);
    }

    function addCategory(ServerRequest $req, Response $res){
        $data = $req->getParsedBody();
        try {
            $category = $this->categoriesRepository->addCategory($data["category-name"]);
        }catch (UniqueConstraintViolationException $e) {
            return $this->responseManager->getResponse(
                EntryPersistedResponse::class,
                new EntryPersisted($res,false,["message" => "the category is already exist"])
            );
        }

        $data = new EntryPersisted($res,true,[
            "cat-name" => $category->getCategoryName(),
            "cat-id"   => $category->getId() 
        ]);

        return $this->responseManager->getResponse(
            EntryPersistedResponse::class,$data
        );
    }
}