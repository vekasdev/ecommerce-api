<?php


namespace App\controllers;

use App\dtos\ResponseDataTransfer;
use App\dtos\UserData;
use App\dtos\UserFiltering;
use App\exceptions\EntityNotExistException;
use App\exceptions\UserValidationException;
use App\model\AuthinticationService;
use App\model\UserServiceFactory;
use App\repositories\UsersRepository;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use responses\JsonResponse;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use User;
use Vekas\ResponseManager\ResponseManager;

class UsersController {
    private UsersRepository $usersRepository;

    function __construct(
        private EntityManager $entityManager,
        private AuthinticationService $authService,
        private ResponseManager $responseManager,
        private UserServiceFactory $userServiceFactory
    ){
        $this->usersRepository = $entityManager->getRepository(User::class);
    }
    function signIn(ServerRequest $req, Response $res){
        // first-name, family-name, password, email, phone-number, address  
        $data = $req->getParsedBody();
        $userData = new UserData(
            $data["first-name"],
            $data["family-name"],
            $this->authService->hashPassword($data["password"]),
            $data["email"],
            $data["phone-number"],
            $data["address"],
        );
        try {
            $user = $this->usersRepository->addUser($userData);
            $userService = $this->userServiceFactory->make($user);
            $userService->generateCode();
            $resData = new ResponseDataTransfer(
                $res,201,[
                    "status"=>"created",
                    "message"=>"Resource has been created but is not yet validated. Further validation is required.",
                    "id" => $user->getId(),
                ]
            );
        } catch (UniqueConstraintViolationException $e) {
                        
            $resData = new ResponseDataTransfer(
                $res,400,[
                    "message" => "the user info is already exist",
                ]
            );
            
        }
        return $this->responseManager->getResponse(JsonResponse::class,$resData); 
    }

    function reGenerateValidationCode(ServerRequest $req, Response $res) {
        // user-id 
        $data = $req->getQueryParams();

        try {
            $user = $this->usersRepository->find((int) $data["user-id"]);
            $service = $this->userServiceFactory->make($user);
            $service->generateCode();
            $dataReturn = new ResponseDataTransfer($res,200,[
                "message" => "code sent successfully"
            ]);
        } catch(EntityNotExistException $e){
            $dataReturn = new ResponseDataTransfer($res,400,[
                "message" => $e->getMessage()
            ]);
        } catch(UserValidationException $e) {
            $dataReturn = new ResponseDataTransfer($res,400,[
                "message" => $e->getMessage()
            ]);
        }

        return $this->responseManager->getResponse(JsonResponse::class,$dataReturn);
    }
    
    function logIn(ServerRequest $req, Response $res) {
        // email, password
        $data = $req->getParams();
        $email = $data["email"];
        $password =$data["password"];
        $filters =  new UserFiltering(email:$email);
        $user = $this->usersRepository->getUser($filters);
        if($user == false) {
            $resData = new ResponseDataTransfer(
                $res,400,[
                    "message" => "user information is wrong",
                ]
            );
        } else {
            $isCorrectPassword = $this->authService->verifyPassword($password,$user);
            if($isCorrectPassword) {
                $token = $this->authService->createSession($user);
                $resData = new ResponseDataTransfer(
                    $res,200,[
                        "token" => $token
                    ]
                );
            } else {
                $resData = new ResponseDataTransfer(
                    $res,400,[
                        "message" => "user information is wrong",
                    ]
                );  
            }
        }
        return $this->responseManager->getResponse(JsonResponse::class,$resData); 
    }

    function validateUserByOTP(ServerRequest $req, Response $res,$args) {
        $userId = $args["user-id"];
        $validationCode = $args["validation-code"];

        try {
            $user= $this->usersRepository->find((int)$userId);
            $userService = $this->userServiceFactory->make($user);
            $userService->validate($validationCode);
            $resData = new ResponseDataTransfer(
                $res,200,[
                    "message" =>  "user validated successfully",
                ]
            );  
        }catch (EntityNotExistException $e) {
            $resData = new ResponseDataTransfer(
                $res,400,[
                    "message" =>  $e->getMessage(),
                ]
            );  
        }catch(UserValidationException $e){
            $resData = new ResponseDataTransfer(
                $res,400,[
                    "message" =>  $e->getMessage(),
                ]
            );  
        }

        return $this->responseManager->getResponse(JsonResponse::class,$resData);
    }
}