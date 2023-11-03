<?php
namespace App\model;

use App\exceptions\EntityNotExistException;
use App\interfaces\CodeValidationSenderInterface;
use App\model\UserService;
use App\repositories\UsersRepository;
use Doctrine\ORM\EntityManager;
use InvalidArgumentException;
use User;

class UserServiceFactory {
    private UsersRepository $usersRepository;
    function __construct(
        private CodeValidationSenderInterface $codeValidationSenderInterface,
        private EntityManager $entityManager,
        private OrderGroupServiceFactory $orderGroupServiceFactory,
        private CartServiceFactory $cartServiceFactory
    ){
        $this->usersRepository = $entityManager->getRepository(User::class);
    }


    function make($user) : UserService{
        if(is_int($user)){
            $user = $this->usersRepository->find($user);
            if(!$user) throw new EntityNotExistException("user given not registered in the app");
        } else if( $user instanceof UserService){}
        else {
            throw new InvalidArgumentException("\$user parameter must be of type int or User class, ".gettype($user)." given");
        }
        
        $us =  new UserService(
            $user,
            $this->entityManager,
            $this->codeValidationSenderInterface,
            $this->orderGroupServiceFactory,
            $this->cartServiceFactory
        );

        return $us;
    }
}