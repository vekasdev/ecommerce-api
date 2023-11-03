<?php

namespace App\model;

use App\exceptions\UserAuthenticationException;
use App\exceptions\UserValidationException;
use App\interfaces\CodeValidationSenderInterface;
use App\repositories\UsersRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use User;
use ValidationCode;

class AuthinticationService{
    // private static $JWT_KEY = base64_encode("49032532KDJSLKFJSDLCSDLJKCMSD984395834KSDLJFKCE3434SDFC");
    private static $JWT_KEY = "49032532KDJSLKFJSDLCSDLJKCMSD984395834KSDLJFKCE3434SDFC";

    private static $ALGORITHM = "HS256";

    const EXPIRATION_PERIOD = 1;

    private UsersRepository $usersRepo;
    function __construct(
        private JWT $jwt,
        private EntityManager $entityManager,
        private UserServiceFactory $userServiceFactory
    ){
        $this->usersRepo = $entityManager->getRepository(User::class);
    }
    
    function createSession(User $user) : string {
        return $this->jwt->encode(
            [
                "iat" => time(),
                "exp" => ((new \DateTime())->modify("+7 day")->getTimestamp()),
                "data" => [
                    "id" => $user->getId(),
                    "admin" => $user->isAdmin()
                ]
            ],
            self::$JWT_KEY,
            self::$ALGORITHM
        );
    }

    /**
     * @throws UserAuthenticationException where the token is not valid
     * @throws UserAuthenticationException where user is not valid , or registeration is not completed
     */
    function verifySession($token){
        
        $result = $this->jwt->decode($token,new Key(self::$JWT_KEY,self::$ALGORITHM));
        
        if(!isset($result->data->id)) throw new  UserAuthenticationException("token is not valid");

        $userService = $this->userServiceFactory->make( (int) $result->data->id);
        if(!$userService->isValid()) throw new UserAuthenticationException("user is not valid , or registeration is not completed");
        return $userService;
        
    }

    function isUserExist(int $user) : bool {
        $user = $this->usersRepo->find($user);
        if(!$user) return false;
        return true;
    }

    function hashPassword(string $password) : string {
        return password_hash($password,PASSWORD_BCRYPT);
    }   

    function verifyPassword($password , User $user){
        return password_verify($password,$user->getPassword());
    }
}