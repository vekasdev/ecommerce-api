<?php

use App\controllers\CategoryController;
use App\controllers\OrdersController;
use App\controllers\ProductController;
use App\controllers\UsersController;
use App\repositories\OrdersRepository;
use Slim\Routing\RouteCollectorProxy;

// $app

$app->group("/api/v1",function(RouteCollectorProxy $app){
    $app->group("/product",function(RouteCollectorProxy $app){
        $app->get("/get-all",[ProductController::class,"getAllProducts"]);
    });
    $app->group("/category",function(RouteCollectorProxy $app){
        $app->post("",[CategoryController::class,"addCategory"]);
    });
    $app->group("/order",function(RouteCollectorProxy $app) {
        $app->post("/add-order",[OrdersController::class,"addOrder"]);
        $app->post("/add-order-to-cart",[OrdersController::class,"addOrdersToCart"]);
        $app->post("/create-cart",[OrdersController::class,"createCart"]);
    });
    $app->group("/user",function (RouteCollectorProxy $app){
        $app->post("/sign-in",[UsersController::class,"signIn"]);
        $app->get("/log-in",[UsersController::class,"logIn"]);
    });
    // requires admin log in
    $app->group("/manage",function(RouteCollectorProxy $app){
        $app->group("/product",function(RouteCollectorProxy $app){
            $app->post("",[ProductController::class,"addProduct"]);
        });
        // update product
        // add category
        // add region and set its cost
        // add disount code 
    });
});
