<?php

use App\controllers\ProductController;
use Slim\Routing\RouteCollectorProxy;

// $app
$app->group("/api/v1/product",function(RouteCollectorProxy $app){
    $app->get("/get-all",[ProductController::class,"getAllProducts"]);
    $app->post("/add-product",[ProductController::class,"addProduct"]);
});