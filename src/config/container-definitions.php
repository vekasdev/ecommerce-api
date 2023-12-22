<?php

use App\interfaces\CodeValidationSenderInterface;
use App\interfaces\NotificationSender;
use App\interfaces\RequestValidatorInterface;
use App\model\AuthinticationService;
use App\model\CartService;
use App\model\CartServiceFactory;
use App\model\EmailServiceFactory;
use App\model\EmailService;
use App\model\ImagesService;
use App\model\OrderGroupServiceFactory;
use App\model\ProductServiceProvider;
use App\model\PromotionAdService;
use App\model\PromotionAdServiceProvider;
use App\model\TelegramAdminstrationNotificationService;
use App\model\UserService;
use App\model\UserServiceFactory;
use App\model\ValidatorFactory;
use App\repositories\UsersRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\Entity;
use Firebase\JWT\JWT;
use GuzzleHttp\Psr7\Request;
use Psr\Container\ContainerInterface;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
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
    ),

    JWT::class => new JWT(),

    EmailServiceFactory::class => new EmailServiceFactory,

    CodeValidationSenderInterface::class => function(ContainerInterface $container)  {
        $factory = $container->get(EmailServiceFactory::class);
        return $factory->makeSmtpGoogleTransporterMailer(
            $container->get("env")["gmail-smtp-config"]["email"],
            $container->get("env")["gmail-smtp-config"]["password"],
        );
    } ,

    AuthinticationService::class => fn(ContainerInterface $container) => new AuthinticationService(
        $container->get(JWT::class),
        $container->get(EntityManager::class),
        $container->get(UserServiceFactory::class)
    ),

    UserServiceFactory::class => fn(ContainerInterface $container)
        => new UserServiceFactory(
            $container->get(CodeValidationSenderInterface::class),
            $container->get(EntityManager::class),
            $container->get(OrderGroupServiceFactory::class),
            $container->get(CartServiceFactory::class)
        ),

    DecoratedResponseFactory::class => function(ContainerInterface $containerInterface) {
        $responseFactory = new ResponseFactory();
        $stream = new StreamFactory();
        
        return new DecoratedResponseFactory($responseFactory,$stream);        
    },

    NotificationSender::class => function(ContainerInterface $containerInterface) 
        { 
            $tn = new TelegramAdminstrationNotificationService();
            return $tn;
        },

    OrderGroupServiceFactory::class => fn(ContainerInterface $containerInterface) 
        => new OrderGroupServiceFactory($containerInterface->get(EntityManager::class),
                                        $containerInterface->get(NotificationSender::class),
                                        $containerInterface->get(CartServiceFactory::class),
                                        $containerInterface
                                    ),
                                        
    
    CartServiceFactory::class => fn(ContainerInterface $containerInterface)
                => new CartServiceFactory($containerInterface->get(EntityManager::class)),


    ProductServiceProvider::class => fn(ContainerInterface $container) 
                => new ProductServiceProvider($container->get(EntityManager::class),$container->get(ImagesService::class)),
    
    PromotionAdServiceProvider::class => fn(ContainerInterface $ci)
                => new PromotionAdServiceProvider($ci->get(EntityManager::class),$ci->get(ImagesService::class))

];