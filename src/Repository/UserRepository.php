<?php

namespace Belga\Repository;

use Belga\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function removeAll()
    {
        $sql = "DELETE FROM user";
        $stmt = $this->_em->getConnection()->prepare($sql);
        $stmt->execute();
    }
}
