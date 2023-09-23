<?php
namespace responses;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\ResponseFactory;
use Vekas\ResponseManager\AbstractResponseEntry;

class productsResponse extends AbstractResponseEntry {
    public ResponseFactory $responseFactory;
    function __construct(ContainerInterface $containerInterface) {
        parent::__construct($containerInterface);
        $this->responseFactory = $this->container->get(ResponseFactory::class);
    }

    function __invoke($data) : ResponseInterface{
        $response =  $this->responseFactory->createResponse(200);
        $response->getBody()->write(json_encode(["product details"=>"some details"]));
        return $response;
    }
}