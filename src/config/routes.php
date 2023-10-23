<?php

use App\controllers\CategoryController;
use App\controllers\OrderGroupController;
use App\controllers\OrdersController;
use App\controllers\ProductController;
use App\controllers\UsersController;
use App\middlewares\AdminUserAuthentication;
use App\middlewares\ForTest;
use App\middlewares\NormalUserAuthentication;
use App\repositories\OrdersRepository;
use Slim\Routing\RouteCollectorProxy;

// $app

$app->group("/api/v1",function(RouteCollectorProxy $app){
    $app->group("/product",function(RouteCollectorProxy $app){
        $app->get("/get-all",[ProductController::class,"getAllProducts"]);
    });
    
    $app->group("/user",function (RouteCollectorProxy $app){
        $app->post("/sign-in",[UsersController::class,"signIn"]);
        $app->get("/send-validation-code",[UsersController::class,"reGenerateValidationCode"]);
        $app->get("/log-in",[UsersController::class,"logIn"]);
        $app->put("/validate-user-by-otp/{user-id:[0-9]+}/{validation-code:[0-9]{4}}",[UsersController::class,"validateUserByOTP"]);
    });

    $app->group("/order",function(RouteCollectorProxy $app){
        $app->post("/set-delivery-data",[OrdersController::class,"setDeliveryData"]);
        $app->put("/addDiscount/{code:[a-z0-9]+}",[OrdersController::class,"addDiscountCode"]);
        $app->get("/get-cost",[OrdersController::class,"getCost"]);
    })->add(NormalUserAuthentication::class);

    //user credentials
    $app->group("/cart",function (RouteCollectorProxy $app) {
        $app->post("/create-order/{product-id:[0-9]+}/{quantity : [0-9]+}",[OrdersController::class,"createOrder"]);
        $app->get("/get-cart-details",[OrdersController::class,"getCartDetails"]);
        $app->put('/change-order-qty/{orderId:[0-9]+}/{valueOfChange:-?\d+}',[OrdersController::class,"changeOrderQty"]);
        $app->put("/confirm-cart",[OrdersController::class,"confirmCart"]); // new
    })->add(NormalUserAuthentication::class);
    
    // requires admin log in
    $app->group("/manage",function(RouteCollectorProxy $app){
        $app->group("/product",function(RouteCollectorProxy $app){
            $app->post("",[ProductController::class,"addProduct"]);
            // update product
            // delete product
        });
        $app->group("/category",function(RouteCollectorProxy $app){
            // add category
            // update category
            $app->post("",[CategoryController::class,"addCategory"]);
        });
        $app->group("/region",function(RouteCollectorProxy $app){
            // add region and set its cost
            // modify existed regions
        });
        $app->group("/disount-code",function(RouteCollectorProxy $app){
            // add disount code 
            // modify discount codes
        });
        $app->group("/order-group",function(RouteCollectorProxy $app){
            
            // getOrderGroups with filtering feature
            $app->get("",[OrderGroupController::class,"getOrderGroups"]);

            // updateOrderGroups
            $app->patch("/mark-as-delivered/{id:[0-9]+}",[OrderGroupController::class,"markAsDelivered"]);

        });
        $app->group("/delivery-region",function(RouteCollectorProxy $app){
            //add delivery region
            //modify existed region
            //remove region
        });
        $app->group("/user",function(RouteCollectorProxy $app){
            // remove
            // band
        });
    });
});
