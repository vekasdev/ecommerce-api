<?php


namespace App\controllers;

use App\dtos\ResponseDataTransfer;
use App\dtos\UserData;
use App\dtos\UserFiltering;
use App\exceptions\EntityNotExistException;
use App\exceptions\MissingCaptchaException;
use App\exceptions\RequestValidatorException;
use App\exceptions\UserValidationException;
use App\model\AuthinticationService;
use App\model\UserServiceFactory;
use App\model\ValidatorFactory;
use App\repositories\RegisterCodesRepository;
use App\repositories\UsersRepository;
use App\validators\SignInValidator;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManager;
use RegisterCode;
use responses\JsonResponse;
use Slim\Http\Response;
use Slim\Http\ServerRequest;
use User;
use Vekas\ResponseManager\ResponseManager;

class UsersController {
    private UsersRepository $usersRepository;
    private RegisterCodesRepository $registerCodesRepository;
    function __construct(
        private EntityManager $entityManager,
        private AuthinticationService $authService,
        private ResponseManager $responseManager,
        private UserServiceFactory $userServiceFactory,
        private ValidatorFactory $validatorFactory
    ){
        $this->usersRepository = $entityManager->getRepository(User::class);
        $this->registerCodesRepository = $entityManager->getRepository(RegisterCode::class);
    }
    function signIn(ServerRequest $req, Response $res){
        // cap-code ,first-name ,family-name, password, email, phone-number, address  
        $data = $req->getParsedBody();

        try {
            $this->validatorFactory->make(SignInValidator::class)->validate($data);
            $userData = new UserData(
                $data["first-name"],
                $data["family-name"],
                $this->authService->hashPassword($data["password"]),
                $data["email"],
                $data["phone-number"],
                $data["address"],
            );

            // create user
            $user = $this->usersRepository->addUser($userData,true);
            $token = $this->authService->createSession($user);
            // here created response
            $res = $res->withJson([
                "token" => $token
            ]);
            
        } catch (UniqueConstraintViolationException $e) {
            $res = $res->withJson([
                "message" => "the user info is already exist",
            ],409);
        } catch (RequestValidatorException $e) {
            $res = $res->withJson($e,400);
        } catch (MissingCaptchaException $cap) {
            $res = $res->withJson("captcha-is-missing",400);
        }
 
        return $res; 
    }

    // function reGenerateValidationCode(ServerRequest $req, Response $res) {
    //     // user-id 
    //     $data = $req->getQueryParams();

    //     try {
    //         $user = $this->usersRepository->find((int) $data["user-id"]);
    //         $service = $this->userServiceFactory->make($user);
    //         $service->generateCode();
    //         $dataReturn = new ResponseDataTransfer($res,200,[
    //             "message" => "code sent successfully"
    //         ]);
    //     } catch(EntityNotExistException $e){
    //         $dataReturn = new ResponseDataTransfer($res,400,[
    //             "message" => $e->getMessage()
    //         ]);
    //     } catch(UserValidationException $e) {
    //         $dataReturn = new ResponseDataTransfer($res,400,[
    //             "message" => $e->getMessage()
    //         ]);
    //     }

    //     return $this->responseManager->getResponse(JsonResponse::class,$dataReturn);
    // }
    
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

    // function validateUserByOTP(ServerRequest $req, Response $res,$args) {
    //     $userId = $args["user-id"];
    //     $validationCode = $args["validation-code"];

    //     try {
    //         $user= $this->usersRepository->find((int)$userId);
    //         $userService = $this->userServiceFactory->make($user);
    //         $userService->validate($validationCode);
    //         $resData = new ResponseDataTransfer(
    //             $res,200,[
    //                 "message" =>  "user validated successfully",
    //             ]
    //         );  
    //     }catch (EntityNotExistException $e) {
    //         $resData = new ResponseDataTransfer(
    //             $res,400,[
    //                 "message" =>  $e->getMessage(),
    //             ]
    //         );  
    //     }catch(UserValidationException $e){
    //         $resData = new ResponseDataTransfer(
    //             $res,400,[
    //                 "message" =>  $e->getMessage(),
    //             ]
    //         );  
    //     }

    //     return $this->responseManager->getResponse(JsonResponse::class,$resData);
    // }
}