<?php

namespace App\Repository;

use App\Entity\SmsText;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SmsText|null find($id, $lockMode = null, $lockVersion = null)
 * @method SmsText|null findOneBy(array $criteria, array $orderBy = null)
 * @method SmsText[]    findAll()
 * @method SmsText[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SmsTextRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SmsText::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SmsText $entity, bool $flush = true): void
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
    public function remove(SmsText $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    public function findValidFromOneMinuteAgo(string $phone): ?array
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

    public function findValidCode(string $phone): ?SmsText
    {
        $where = 'c.valid = :val AND c.phone = :phone AND c.code IS NOT NULL ';
        return $this->createQueryBuilder('c')
            ->where($where)
            ->setParameter('val', true)
            ->setParameter('phone', $phone)
            ->getQuery()
            ->getOneOrNullResult();
    }

}
