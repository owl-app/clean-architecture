<?php

declare(strict_types=1);

namespace Owl\Shared\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Owl\Shared\Domain\Persistence\BaseEntityInterface;

abstract class DoctrineRepository extends ServiceEntityRepository
{
    public function persist(BaseEntityInterface $entity): void
    {
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush($entity);
    }

    public function remove(BaseEntityInterface $entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush($entity);
    }
}
