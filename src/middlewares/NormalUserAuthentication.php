<?php
namespace App\middlewares;

use App\exceptions\UserAuthenticationException;
use App\model\AuthinticationService;
use App\model\UserServiceFactory;
use Firebase\JWT\ExpiredException;
use Firebase\JWT\SignatureInvalidException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Http\Factory\DecoratedResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Response;
use UnexpectedValueException;
use Vekas\ResponseManager\ResponseManager;

class NormalUserAuthentication {
    function __construct(
        private UserServiceFactory $userServiceFactory,
        private AuthinticationService $authinticationService,
        private DecoratedResponseFactory $decoratedResponseFactory,
        private ResponseManager $responseManager
    ){}
    function __invoke(ServerRequestInterface $request , RequestHandler $requestHandler) : ResponseInterface{

        try {
            if(!$request->hasHeader("Authorization")){
                $response = $this->createJsonResponse(403);
                $response->getBody()->write(json_encode(
                    [
                        "message" => "the user must be authorized "
                    ]));
                return $response;
            }
            $token = $request->getHeader("Authorization")[0];

            // get token
            list($JWTtoken) = sscanf($token,"Bearer %s");

            // check tokens and return when success user service
            $userService = $this->authinticationService->verifySession($JWTtoken);

            //return user service as attribute as user key
            $request = $request->withAttribute("user",$userService);
            
            // pass the other routes
            $response = $requestHandler->handle($request);

        } catch(UserAuthenticationException $e) {
            $response = $this->createJsonResponse(403);
            $response->getBody()->write(json_encode(
                [
                    "message" => $e->getMessage()
                ]
            )
            );
        } catch(UnexpectedValueException $e) {
            $response = $this->createJsonResponse(403);
            $response->getBody()->write(json_encode(
                [
                    "message" => "token is not valid"
                ]
            )
            );
        } catch(ExpiredException $e){
            $response = $this->createJsonResponse(403);
            $response->getBody()->write(json_encode(
                [
                    "message" => "token is expired"
                ]
            )
            );
        } catch(SignatureInvalidException $e) {
            $response = $this->createJsonResponse(400);
            $response->getBody()->write(json_encode(
                [
                    "message" => "token is not valid"
                ]
            )
            );
        }

        return $response;
    }


    function createJsonResponse($status) {
        return ($this->decoratedResponseFactory->createResponse($status))
            ->withHeader("ContentType","application/json");
    }
}