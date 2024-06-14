<?php

namespace App\middlewares;

use App\exceptions\MissingCaptchaException;
use App\repositories\RegisterCodesRepository;
use Doctrine\ORM\EntityManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use RegisterCode;
use Slim\Handlers\Strategies\RequestHandler;
use Slim\Http\Factory\DecoratedResponseFactory;
use Slim\Interfaces\MiddlewareDispatcherInterface;


class CaptchaCodeMiddleware  {

    private RegisterCodesRepository $registerCodesRepository;
    function __construct(
        private EntityManager $entityManager,
        private DecoratedResponseFactory $responseFactory
    ){
        $this->registerCodesRepository = $entityManager->getRepository(RegisterCode::class);
    }
    function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ) : ResponseInterface {

        $captchaCode = $request->getQueryParams()["captcha-code"];
        if (!$captchaCode) {
            // create new response
            $res = $this->responseFactory->createResponse(400);
            $res = $res->withJson([
                "captcha-code" => [
                    "message" => "captcha-code is required"
                ]
            ],400);
            return $res;
        }

        $regCode = $this->registerCodesRepository->getByCode($captchaCode);

        if( (!$regCode) || $regCode->getExpire() < time() ){
            // create new response
            $res = $this->responseFactory->createResponse(400);
            $res = $res->withJson([
                "captcha-code" => [
                    "message" => "captcha-code is not valid"
                ]
            ],400);
            return $res;
        } else {
            $this->registerCodesRepository->delete($regCode->getId());
           return $handler->handle($request);
        }
        
    }
    
}