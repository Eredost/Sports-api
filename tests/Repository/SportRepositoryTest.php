<?php

namespace App\Tests\Repository;

use App\Entity\Sport;

class SportRepositoryTest extends AbstractRepositoryTest
{
    public function testFindOne(): void
    {
        $sport = $this->entityManager
            ->getRepository(Sport::class)
            ->findOneBy([])
        ;

        self::assertInstanceOf(Sport::class, $sport);
    }

    public function testGetSports(): void
    {
        $sports = $this->entityManager
            ->getRepository(Sport::class)
            ->getSports(0, 5, 'asc')
        ;

        self::assertCount(5, $sports);
        self::assertContainsOnlyInstancesOf(Sport::class, $sports);
    }
}
