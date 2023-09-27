<?php
namespace responses;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\ResponseFactory;
use Vekas\ResponseManager\AbstractResponseEntry;
use App\dtos\ResponseDataTransfer;
class JsonResponse extends AbstractResponseEntry {
    public ResponseFactory $responseFactory;
    function __construct(ContainerInterface $containerInterface) {
        parent::__construct($containerInterface);
    }

    /**
     * @param ResponseDataTransfer $data
     */
    function __invoke($data) : ResponseInterface{
        $data->res = $data->res->withStatus($data->statusCode);
        $data->res = $data->res->withHeader("Content-Type","application/json");
        $data->res->getBody()->write(json_encode($data->data));
        return $data->res;
    }
}