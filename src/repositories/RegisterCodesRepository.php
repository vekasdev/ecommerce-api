<?php
namespace App\repositories;
use App\exceptions\EntityNotExistException;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityRepository;

class RegisterCodesRepository extends EntityRepository {
    function create(string $code ,int $expire){
        $rc = new \RegisterCode();
        $rc->setCode($code);
        $rc->setExpire($expire);
        
        $this->getEntityManager()->persist($rc);
        $this->getEntityManager()->flush();

        return $rc;
    }

    function delete($registerCodeId) {
        if(!$rc = $this->find($registerCodeId)) {
            throw new EntityNotExistException($this->getClassName()." with id : $registerCodeId not exist");
        }

        $this->getEntityManager()->remove($rc);
        $this->getEntityManager()->flush();
    }

    /**
     * @return \RegisterCode | null
     */
    function getByCode($code) {
        $qb = $this->createQueryBuilder("rc");
        $qb->select("rc")
        ->where("rc.code = :code")
        ->setParameter("code",$code);
        return $qb->getQuery()->getOneOrNullResult();
    }

    function clearExpired() {
        $qb = $this->createQueryBuilder("rc");
        $qb->select("rc")
            ->where($qb->expr()->lt("rc.expire",":current"))
            ->setParameter("current",time());

        $results = $qb->getQuery()->getResult();
        foreach($results as $result) {
            $this->getEntityManager()->remove($result);
        }
        $this->getEntityManager()->flush();
    }

}