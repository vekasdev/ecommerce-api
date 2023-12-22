<?php

use App\controllers\CategoryController;
use App\controllers\ColorController;
use App\controllers\DeliveryRegionController;
use App\controllers\DiscountCodeController;
use App\controllers\ImageController;
use App\controllers\MainCategoriesController;
use App\controllers\OrderGroupController;
use App\controllers\OrdersController;
use App\controllers\ProductController;
use App\controllers\PromotionAdController;
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
        $app->patch("/product-interest-toggle/{product-id:[0-9]+}",[ProductController::class,"productInterestToggle"])
            ->add(NormalUserAuthentication::class);
        $app->get("/is-product-in-the-interest-list/{user-id:[0-9]+}/{product-id:[0-9]+}",
            [ProductController::class,"isProductInTheInterestList"]);
        $app->get("/{id:[0-9]+}",[ProductController::class,"getProduct"]);
    });

    $app->group("/image",function(RouteCollectorProxy $app){
        $app->get("",[ImageController::class,"getImage"]);
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
        $app->get("/get-default-delivery-data",[OrdersController::class,"getDefaultDeliveryData"]);
    })->add(NormalUserAuthentication::class);

    $app->group("/category",function(RouteCollectorProxy $app){
        $app->get("/high-level",[MainCategoriesController::class,"getAll"]);
        $app->get("/low-level",[CategoryController::class,"getAll"]);
        $app->get("/{id:[0-9]+}",[CategoryController::class,"getCategory"]);
    });

    //user credentials
    $app->group("/cart",function (RouteCollectorProxy $app) {
        $app->post("/create-order/{product-id:[0-9]+}/{quantity : [0-9]+}",[OrdersController::class,"createOrder"]);
        $app->get("/get-cart-details",[OrdersController::class,"getCartDetails"]);
        $app->put('/change-order-qty/{orderId:[0-9]+}/{valueOfChange:-?\d+}',[OrdersController::class,"changeOrderQty"]);
        $app->put("/confirm-cart",[OrdersController::class,"confirmCart"]); // new
        $app->get("/get-orders-count",[OrdersController::class,"getOrdersCount"]);
    })->add(NormalUserAuthentication::class);
    
    $app->group("/delivery-region",function (RouteCollectorProxy $app){
        $app->get("",[DeliveryRegionController::class,"getAll"]);
    })->add(NormalUserAuthentication::class);

    $app->group("/order-group",function(RouteCollectorProxy $app){
        $app->get("",[OrderGroupController::class,"getCurrentUserOrderGroups"]);
    })->add(NormalUserAuthentication::class);

    $app->group("/manage",function(RouteCollectorProxy $app){
        
        $app->group("/product",function(RouteCollectorProxy $app){
            $app->post("",[ProductController::class,"addProduct"]);
            $app->post("/update/{id:[0-9]+}",[ProductController::class,"updateProduct"]);
            $app->delete("/{id:[0-9]+}",[ProductController::class,"deleteProduct"]);
        });

        $app->group("/category",function(RouteCollectorProxy $app){
            $app->post("",[CategoryController::class,"addCategory"]);
            $app->put("/{id:[0-9]+}",[CategoryController::class,"updateCategory"]);
            $app->delete("/{id:[0-9]+}",[CategoryController::class,"removeCategory"]);
        });

        $app->group("/main-category",function ( RouteCollectorProxy $app){
            $app->post("",[MainCategoriesController::class,"createMainCategory"]);
            $app->put("/{id:[0-9]+}",[MainCategoriesController::class,"updateMainCategory"]);
            $app->delete("/{id:[0-9]+}",[MainCategoriesController::class,"deleteCategory"]);
        });
        $app->group("/discount-code",function(RouteCollectorProxy $app){
            $app->post("",[DiscountCodeController::class,"createDiscountCode"]);
            $app->post("/update/{id:[0-9]+}",[DiscountCodeController::class,"updateDiscountCode"]);
            $app->get("",[DiscountCodeController::class,"getDiscountCodes"]);
        });
        $app->group("/order-group",function(RouteCollectorProxy $app){
            $app->get("",[OrderGroupController::class,"getOrderGroups"]);
            $app->patch("/mark-as-delivered/{id:[0-9]+}",[OrderGroupController::class,"markAsDelivered"]);
            $app->patch("/dismiss-order/{id:[0-9]+}",[OrderGroupController::class,"dismissOrder"]);
        });
        $app->group("/delivery-region",function(RouteCollectorProxy $app){
            $app->get("",[DeliveryRegionController::class,"getAll"]);
            $app->post("",[DeliveryRegionController::class,"addDeliveryRegion"]);
            $app->put("/{id:[0-9]+}",[DeliveryRegionController::class,"updateDeliveryRegion"]);
        });
        $app->group("/user",function(RouteCollectorProxy $app){
            // remove
            // band , user , band its ip's stored in the databases
        });

        $app->group("/promotion-ad",function(RouteCollectorProxy $app){
            $app->post("",[PromotionAdController::class,"addPromotionAd"]);
            $app->get("",[PromotionAdController::class,"getPromotionAds"]);
            $app->delete("/{id:[0-9]+}",[PromotionAdController::class,"deletePromotionAd"]);
            $app->post("/update/{id:[0-9]+}",[PromotionAdController::class,"updatePromotionAd"]);
        });

        $app->group("/color",function(RouteCollectorProxy $app){
            $app->get("/get-all",[ColorController::class,"getColors"]);
        });

    });
});
