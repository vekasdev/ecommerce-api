<?php

use App\interfaces\RequestValidatorInterface;
use App\model\ImagesService;
use App\model\ValidatorFactory;
use Doctrine\ORM\EntityManager;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;
use Tuupola\Http\Factory\ResponseFactory;
use Vekas\ResponseManager\FileLoader;
use Vekas\ResponseManager\ResponseManagerFactory;
use Vekas\ResponseManager\ResponseManager;

return [
    "env" => require __DIR__ . "/env.php",
    ResponseFactory::class => new ResponseFactory,
    ResponseManager::class => function(ContainerInterface $container) {
        $rm = new ResponseManagerFactory;
        $fileLoader = new FileLoader("Response",$container->get("env")["base-path"]."/src/responses","responses");
        $rm->setFileLoader($fileLoader);
        return $rm->getResponseManager($container);
    },
    ...require_once __DIR__."/validators-definitions.php", // validators
    ValidatorFactory::class => fn(ContainerInterface $container) => new ValidatorFactory($container),
    EntityManager::class => require_once __DIR__."/../../bootstrap.php",
    ImagesService::class => fn(ContainerInterface $container) => new ImagesService(
        $container->get("env")["imageUploadConfig"]["storageDirectory"],
        $container->get("env")["imageUploadConfig"]["acceptedExtensions"],
        $container->get("env")["imageUploadConfig"]["maxSize"]
    )
];