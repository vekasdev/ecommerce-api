<?php

use Doctrine\ORM\AbstractQuery;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;
use Doctrine\ORM\EntityManager;
use PromotionAd;



require __DIR__."/../vendor/autoload.php";

// $jwt = new JWT;


// $result = $jwt->decode($token,new Key("secret","HS256"));

// var_dump($result);


// $responseFactory = new ResponseFactory();
// $stream = new StreamFactory();

// $decorated = new DecoratedResponseFactory($responseFactory,$stream);





/** @var  EntityManager  */
$em = require_once __DIR__."/../bootstrap.php";

/** @var  PromotionAd */
$ad = $em->find(PromotionAd::class,4);


$image = $ad->getImage();

$em->remove($image);
$em->flush();
 