<?php
use DI\ContainerBuilder;
use Slim\App;
use Slim\Factory\AppFactory;
use Slim\Middleware\BodyParsingMiddleware;

require_once "vendor/autoload.php";
require_once "src/enums/APPENUMS.php";
include      "src/config/functions.php";

// container creation
$containerDefinitions = require_once("src/config/container-definitions.php");
$container = new ContainerBuilder();
$container->addDefinitions($containerDefinitions);
$container = $container->build();


$app = AppFactory::create(container:$container);

// add parsing body middleware 
$app->add(new BodyParsingMiddleware() );
// add error middleware
$app->addErrorMiddleware(true,true,true);

// routes
require "src/config/routes.php";


$app->run();