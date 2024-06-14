<?php


namespace App\repositories;

use App\dtos\UserData;
use App\dtos\UserFiltering;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use User;
use ValidationCode;

class UsersRepository extends EntityRepository {
    function addUser(UserData $userData,bool $valid){
        $user = new User();
        $user->setAddress($userData->address)
        ->setEmail($userData->email)
        ->setFamilyName($userData->familyName)
        ->setFirstName($userData->firstName)
        ->setPassword($userData->password)
        ->setPhoneNumber($userData->phoneNumber)
        ->setValid($valid);

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }

    function getUser(UserFiltering $filter) : bool | User{
        $qb = $this->createQueryBuilder("u");
        $query = $qb->select("u");

        if(isset($filter->email)) {
            $query->andWhere($qb->expr()->eq("u.email",":email"));
            $query->setParameter("email",$filter->email);
        }

        if(isset($filter->phoneNumber)) {
            $query->andWhere($qb->expr()->eq("u.phoneNumber",":phoneNumber"));
            $query->setParameter("phoneNumber",$filter->phoneNumber);
        }

        if(isset($filter->password)) {
            $query->andWhere($qb->expr()->eq("u.password",":password"));
            $query->setParameter("password",$filter->password);
        }

        try {
            $result = $query->getQuery()->getSingleResult();
        } catch(NoResultException $noResultException) {
            return false;
            // must handling the others exceptions the method throw's
        }

        return $result;
    }
}