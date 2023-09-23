<?php


namespace responses;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tuupola\Http\Factory\ResponseFactory;
use Vekas\ResponseManager\AbstractResponseEntry;
use App\dtos\EntryPersisted;
class EntryPersistedResponse extends AbstractResponseEntry {
    public ResponseFactory $responseFactory;
    function __construct(ContainerInterface $containerInterface) {
        parent::__construct($containerInterface);
        $this->responseFactory = $this->container->get(ResponseFactory::class);
    }

    /**
     * @param EntryPersisted $data 
    */
    function __invoke($data) : ResponseInterface{
        if($data->successed){
            $response =  $this->responseFactory->createResponse(200);
        }else {
            $response =  $this->responseFactory->createResponse(200);
        }
        $response = $response->withHeader("Content-Type","application/json");
        $response->getBody()->write(json_encode($data->data));
        return $response;
    }
}