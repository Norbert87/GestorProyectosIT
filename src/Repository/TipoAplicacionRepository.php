<?php

namespace App\Repository;

use App\Entity\TipoAplicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TipoAplicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method TipoAplicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method TipoAplicacion[]    findAll()
 * @method TipoAplicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TipoAplicacionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TipoAplicacion::class);
    }

    // /**
    //  * @return TipoAplicacion[] Returns an array of TipoAplicacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TipoAplicacion
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
