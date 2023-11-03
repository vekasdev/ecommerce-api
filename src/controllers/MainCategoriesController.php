<?php


namespace App\controllers;

use App\repositories\MainCategoriesRepository;
use App\dtos\EntryPersisted;
use App\exceptions\EntityNotExistException;
use App\exceptions\RequestValidatorException;
use App\model\ValidatorFactory;
use App\repositories\CategoriesRepository;
use App\validators\AddCategoryValidator;
use App\validators\CreateMainCategoryValidator;
use Category;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use Vekas\ResponseManager\ResponseManager;
use MainCategory;

use function DI\create;

class MainCategoriesController {
    private MainCategoriesRepository $mainCategoriesRepository;
    private CategoriesRepository $categoriesRepository;
    function __construct(
        private EntityManager $entityManager,
        private ValidatorFactory $validatorFactory
    ) {
        $this->mainCategoriesRepository = $this->entityManager->getRepository(MainCategory::class);
        $this->categoriesRepository = $this->entityManager->getRepository(Category::class);
    }

    function getAll(ServerRequest $request, Response $response, array $args): Response {
        $categories = $this->mainCategoriesRepository->getAll();
        
        return $response->withJson($categories);
    }


    function createMainCategory(ServerRequest $req, Response $res, array $args){
        $data = $req->getParsedBody();
        try {
            $this->validatorFactory->make(CreateMainCategoryValidator::class)->validate($data);
            $cat = $this->mainCategoriesRepository->create($data["name"],$this->getCategoriesFromCSV($data["categories"]));
            $subCategories = [];
            foreach($cat->getCategories() as $subCategory){
                $subCategories[] = $subCategory->getCategoryName();
            }
            $res = $res->withJson([
                "status"=> "success",
                "message"=> "the category created successfully",
                "category-id" => $cat->getId(),
                "category-name" => $cat->getName(),
                "sub-categories" => $subCategories
            ] ,200);
        } catch (UniqueConstraintViolationException $e) { 
            $res = $res->withJson(["status"=> "failure","message" => "data provided is already exist , try another one"] ,400);
        } catch (RequestValidatorException $e) { 
            $res = $res->withJson($e,400);
        }

        return $res;
    }

    function updateMainCategory(ServerRequest $req, Response $res, array $args) {
        $data = $req->getParams();
        $id = (int) $args["id"];
        try {
            $this->validatorFactory->make(CreateMainCategoryValidator::class)->validate($data);
            $cat = $this->mainCategoriesRepository->update($id , $data["name"] , $this->getCategoriesFromCSV($data["categories"]));
            $subCategories = [];
            foreach($cat->getCategories() as $subCategory){
                $subCategories[] = [
                    "name" => $subCategory->getCategoryName(),
                    "id" => $subCategory->getId()
                ];
            }
            $res = $res->withJson([
                "status"=> "success",
                "message"=> "the category updated successfully",
                "category-id" => $cat->getId(),
                "category-name" => $cat->getName(),
                "sub-categories" => $subCategories
            ] ,200);
        } catch (UniqueConstraintViolationException $e) { 
            $res = $res->withJson(["status"=> "failure","message" => "data provided is already exist , try another one"] ,400);
        } catch (RequestValidatorException $e) { 
            $res = $res->withJson($e,400);
        } catch (EntityNotExistException $e) {
            $res = $res->withJson(["message" => $e->getMessage()],400);
        }
        return $res;
    }


    function deleteCategory(ServerRequest $req, Response $res, array $args) {
        $id = (int) $args["id"];
        try {
            $this->mainCategoriesRepository->delete($id);
            $res = $res->withJson(["status"=> "success","message"=> "category deleted successfully","id"=>$id] ,200);
        } catch (EntityNotExistException $e) { 
            $res = $res->withJson(["status"=> "failure","message" => "category is not exist"],400);
        }
        return $res;
    }

    /**
     * @return Category[]
     */
    function getCategoriesFromCSV($csv) {
        $ids = CSV2ARRAY($csv);
        $categories  = [];
        foreach ( $ids as $id ) {
            $_category = $this->categoriesRepository->find($id);
            if(!$_category) throw new EntityNotExistException("category with id : ".$id." not exist");
            array_push($categories, $_category);
        }
        return $categories;
    }

}