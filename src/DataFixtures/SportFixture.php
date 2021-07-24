<?php

namespace App\DataFixtures;

use App\Entity\Sport;
use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Providers\SportProvider;

class SportFixture extends AbstractFixture
{
    protected function loadData(ObjectManager $manager): void
    {
        $sports = SportProvider::getSports();

        $this->createMany(count($sports), 'sports', function ($i) use ($sports) {
            return (new Sport())
                ->setLabel($sports[$i])
                ->setCreatedAt($this->randomDateBetween(
                    new \DateTime('2 months ago'),
                    new \DateTime('1 month ago')
                ))
                ->setUpdatedAt($this->randomDateBetween(
                    new \DateTime('1 months ago'),
                    new \DateTime('now')
                ))
            ;
        });

        $manager->flush();
    }
}
