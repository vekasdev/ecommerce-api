<?php


namespace App\controllers;

use App\dtos\EntryPersisted;
use App\exceptions\EntityNotExistException;
use App\exceptions\RequestValidatorException;
use App\model\ValidatorFactory;
use App\repositories\CategoriesRepository;
use App\validators\AddCategoryValidator;
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
        private ResponseManager $responseManager,
        private ValidatorFactory $validatorFactory
    ) {
        $this->categoriesRepository = $entityManager->getRepository(Category::class);
    }

    function getAll(ServerRequest $request, Response $response, array $args): Response {
        return $response->withJson($this->categoriesRepository->getAll());
    }
    
    function getCategory(ServerRequest $req, Response $res, array $args) {
        $id = (int) $args["id"];
        try { 
            /** @var Category $category */
            if (! $category = $this->categoriesRepository->find($id)) {
                throw new EntityNotExistException ("category with id $id not exist");
            }
            $res = $res->withJson([
                "name" => $category->getCategoryName(),
                "id" => $category->getId()
            ]);
        } catch(EntityNotExistException $e ) {
            $res = $res->withJson(["message"=>$e->getMessage()],400);
        }
        return $res;
    }

    function updateCategory(ServerRequest $request, Response $response, array $args): Response {
        $id = $args["id"];
        $data = $request->getParams();
        try {
            $this->validatorFactory->make(AddCategoryValidator::class)->validate($data);
            $category = $this->categoriesRepository->updateCategory($id,$data["name"]);
            $response = $response->withJson([
                "success"=> "successful",
                "message" => "category : ".$category->getCategoryName() ." updated successfully"
            ]);
        } catch (UniqueConstraintViolationException $e) {
            $response = $response->withJson([
                "status" => "failure",
                "message" => "category name is exist try another not duplicated values"
            ],400);
        } catch (RequestValidatorException $e) {
            $response = $response->withJson($e,400);
        } catch (EntityNotExistException $e) {
            $response = $response->withJson([
                "status" => "failure",
                "message" => "category is not exist to modify try another one"
            ],400);
        }

        return $response;
    }
    function addCategory(ServerRequest $req, Response $res){
        $data = $req->getParsedBody();
        try {
            $category = $this->categoriesRepository->addCategory($data["name"]);
            $res = $res->withJson(["status"=> "success",
                "cat-name" => $category->getCategoryName(),
                "cat-id"   => $category->getId() 
            ],200);
        }catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson(["status"=> "failure","message"=> "category is already exist"],400);
        }catch (RequestValidatorException $e) {
            $res= $res->withJson($e,400);
        }

        return $res;
    }

    function removeCategory( ServerRequest $req , Response $res,$args )  {
        $catId = $args["id"];
        try {
            $categoryRemoved = $this->categoriesRepository->removeCategory($catId);
            $res = $res->withJson(["status"=> "success",
                "message"=> "category".$categoryRemoved->getCategoryName()." removed successfully"],200);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["status"=> "failure","message"=> $e->getMessage()],400);
        }

        return $res;
    }

}