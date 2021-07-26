<?php

namespace App\Tests\Repository;

use App\Entity\User;

class UserRepositoryTest extends AbstractRepositoryTest
{
    public function testFindOne(): void
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([])
        ;

        self::assertInstanceOf(User::class, $user);
    }
}
