<?php


// $token = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiaWF0IjoxNTE2MjM5MDIyLCJkYXRhIjp7ImlkIjoiMiJ9fQ.Rl0rGWK0rgY-33DL5u5lwFkaUglrROqgF47JfqdhLgE";

use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Psr7\Factory\StreamFactory;

require __DIR__."/../vendor/autoload.php";

// $jwt = new JWT;


// $result = $jwt->decode($token,new Key("secret","HS256"));

// var_dump($result);


// $responseFactory = new ResponseFactory();
// $stream = new StreamFactory();

// $decorated = new DecoratedResponseFactory($responseFactory,$stream);



$em = require_once __DIR__."/../bootstrap.php";

/**
 * @var OrderGroup
 */
$og = $em->find(OrderGroup::class,8);

$cart = $og->getCart();

echo $cart->getOrders()->count();