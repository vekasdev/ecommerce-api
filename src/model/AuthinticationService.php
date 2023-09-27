<?php

namespace App\model;

use App\repositories\UsersRepository;
use Doctrine\ORM\EntityManager;
use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use User;

class AuthinticationService{
    // private static $JWT_KEY = base64_encode("49032532KDJSLKFJSDLCSDLJKCMSD984395834KSDLJFKCE3434SDFC");
    private static $JWT_KEY = "49032532KDJSLKFJSDLCSDLJKCMSD984395834KSDLJFKCE3434SDFC";

    private static $ALGORITHM = "HS256";

    private UsersRepository $usersRepo;
    function __construct(
        private JWT $jwt,
        private EntityManager $entityManager
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


    function verifySession($string){
        try {
            $this->jwt->decode($string,new Key(self::$JWT_KEY,self::$ALGORITHM));
        } catch (Exception $e) {
            return false;
        }
        return true;
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