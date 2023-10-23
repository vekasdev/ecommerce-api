<?php
namespace App\model;

use App\exceptions\EntityNotExistException;
use App\interfaces\CodeValidationSenderInterface;
use App\model\UserService;
use Doctrine\ORM\EntityManager;
use User;

class UserServiceFactory {
    function __construct(
        private CodeValidationSenderInterface $codeValidationSenderInterface,
        private EntityManager $entityManager,
        private OrderGroupServiceFactory $orderGroupServiceFactory,
        private CartServiceFactory $cartServiceFactory
    ){}

    /**
     * @param \User $user
     */
    function make(User $user) :UserService{
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