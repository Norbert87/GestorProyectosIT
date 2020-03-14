<?php

namespace App\Repository;

use App\Entity\Usuario;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }

    // /**
    //  * @return Usuario[] Returns an array of Usuario objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Usuario
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findComerciales()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT u
                      FROM App:Usuario u
                      WHERE u.roles like :rol and u.active=1')
            ->setParameter('rol','%ROLE_COMERCIAL%');

        try
        {
            return $query->getResult();
        }
        catch (NoResultException $e)
        {
            return null;
        }
    }

    public function findJefesproyecto()
    {
        $query = $this->getEntityManager()
            ->createQuery('SELECT u
                      FROM App:Usuario u
                      WHERE u.roles like :rol and u.active=1')
            ->setParameter('rol','%ROLE_JEFEPROYECTO%');

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
