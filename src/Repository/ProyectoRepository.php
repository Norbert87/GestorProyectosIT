<?php

namespace App\Repository;

use App\Entity\Proyecto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Proyecto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Proyecto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Proyecto[]    findAll()
 * @method Proyecto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProyectoRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Proyecto::class);
    }

    // /**
    //  * @return Proyecto[] Returns an array of Proyecto objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Proyecto
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findUsuariosSinProyecto($arrayids)
    {
        $dql = "SELECT u.id,u.email 
                      FROM App:Usuario u
                      WHERE  u.roles like '%ROLE_TECNICO%' and u.active=1";

        if(count($arrayids)>0)
        {
            $dql.="and u.id not in (:array)";
        }
        $query = $this->getEntityManager()->createQuery($dql);
        if(count($arrayids)>0) {
            $query->setParameter('array', $arrayids);
        }
        try
        {
            return $query->getArrayResult();
        }
        catch (NoResultException $e)
        {
            return null;
        }
    }

    public function findProyectosByTecnico($tecnicoid)
    {
        $dql = "SELECT p
                      FROM App:Proyecto p
                      JOIN p.tecnicos t
                      WHERE t.id = :tencnicoid ";


        $query = $this->getEntityManager()->createQuery($dql);
        $query->setParameter('tencnicoid', $tecnicoid);

        try
        {
            return $query->getResult();
        }
        catch (NoResultException $e)
        {
            return null;
        }
    }
}
