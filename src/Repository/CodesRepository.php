<?php

namespace App\Repository;

use App\Entity\Codes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Codes|null find($id, $lockMode = null, $lockVersion = null)
 * @method Codes|null findOneBy(array $criteria, array $orderBy = null)
 * @method Codes[]    findAll()
 * @method Codes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Codes::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(Codes $entity, bool $flush = true): void
    {
        $this->_em->persist($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function remove(Codes $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findValidFromOneMinuteAgo($phone): ?array
    {
        $date = new \DateTime('1 minute ago');
        return $this->createQueryBuilder('c')
            ->andWhere('c.sendAt > :send')
            ->setParameter('send', $date->format('Y-m-d H:i:s'))
            ->andWhere('c.valid = :val')
            ->setParameter('val', true)
            ->andWhere('c.phone = :phone')
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getResult();
    }

}
