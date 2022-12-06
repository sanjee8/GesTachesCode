<?php

namespace App\Repository;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function add(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @return Task[] Returns an array of Task objects
     */
    public function findByCreateBy($value): array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.create_by = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @return ArrayCollection Returns an array of Task objects
     */
    public function findByTaskOf(User $user) {

        $tasks_created = new ArrayCollection($this->findByCreateBy($user->getId()));

        $tasks_collabs = $user->getITasks();

        return new ArrayCollection(
            array_merge($tasks_created->toArray(), $tasks_collabs->toArray())
        );

    }

    /**
     * @return ArrayCollection Returns an array of Task objects
     */
    public function findByTaskOfLimit(User $user, int $limit) {

        $tasks_created = new ArrayCollection($this->findByCreateBy($user->getId()));

        $tasks_created = $tasks_created->filter(
            function ($entry) {

                return $entry->getPourcent() < 100;

            }
        );

        $tasks_collabs = $user->getITasks()->filter(
            function ($entry) {
                return $entry->getPourcent() < 100;
            }
        );

        return new ArrayCollection(
            array_slice(array_merge($tasks_created->toArray(), $tasks_collabs->toArray()), 0, $limit)
        );

    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
