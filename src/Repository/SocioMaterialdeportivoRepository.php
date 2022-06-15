<?php

namespace App\Repository;

use App\Entity\SocioMaterialdeportivo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SocioMaterialdeportivo|null find($id, $lockMode = null, $lockVersion = null)
 * @method SocioMaterialdeportivo|null findOneBy(array $criteria, array $orderBy = null)
 * @method SocioMaterialdeportivo[]    findAll()
 * @method SocioMaterialdeportivo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SocioMaterialdeportivoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SocioMaterialdeportivo::class);
    }

    /**
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function add(SocioMaterialdeportivo $entity, bool $flush = true): void
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
    public function remove(SocioMaterialdeportivo $entity, bool $flush = true): void
    {
        $this->_em->remove($entity);
        if ($flush) {
            $this->_em->flush();
        }
    }

    // /**
    //  * @return SocioMaterialdeportivo[] Returns an array of SocioMaterialdeportivo objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SocioMaterialdeportivo
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
