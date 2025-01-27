<?php

namespace App\Repository;

use App\Entity\Customer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityRepository;
use Doctrine\DBAL\Connection;

/**
 * @extends ServiceEntityRepository<Customer>
 */
class CustomerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Customer::class);
    }
    public function findUsersByCustomerId($customerId): array
    {
        return $this->createQueryBuilder('c')
        ->select('u.id') // Récupérer les ID des utilisateurs
        ->join('c.user', 'u') // Joindre la relation ManyToMany
        ->where('c.customer = :customerId')
        ->setParameter('customerId', $customerId->getId())
        ->getQuery()
        ->getSingleColumnResult();

    }

    public function isAccepted($id_user_admin , $id_user){
        return $this->createQueryBuilder('c')
        ->select('c.id') // Récupérer l'ID de l'utilisateur
        ->where('c.customer = :customerId') // Filtrer par l'ID du Customer
        ->andWhere('c.user = :userId') // Filtrer par l'ID de l'Utilisateur
        ->setParameter('customerId', $id_user_admin) // ID du Customer
        ->setParameter('userId', $id_user) // ID du User
        ->getQuery()
        ->getSingleScalarResult(); // Retourne un seul ID (ou null si aucun)
    }
//    /**
//     * @return Customer[] Returns an array of Customer objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Customer
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
