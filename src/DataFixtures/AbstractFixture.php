<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

abstract class AbstractFixture extends Fixture
{
    private ObjectManager $manager;

    private array $referencesIndex = [];

    abstract protected function loadData(ObjectManager $manager);

    /**
     * Method called when the doctrine:fixtures:load command is used
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        $this->manager = $manager;

        $this->loadData($manager);
    }

    /**
     * Allows the creation of multiple entities more simply
     *
     * $this->createMany(10, 'main_user', function($count) {
     *     $user = (new User())
     *         ->setUsername(sprintf('user%d', $count))
     *         ->setPassword('password')
     *     ;
     *     return $user;
     * });
     *
     * @param int      $count     The number of entities that will be created
     * @param string   $groupName The name that will be used to reference the entity
     * @param callable $factory   Will add the necessary fields to the entity
     *
     * @return void
     * @throws \LogicException when the entity created in the function was not returned
     */
    protected function createMany(int $count, string $groupName, callable $factory): void
    {
        for ($i = 0; $i < $count; $i++) {
            $entity = $factory($i);

            if (null === $entity) {
                throw new \LogicException(
                    'Did you forget to return the entity object from your callback to AbstractFixture::createMany()?'
                );
            }
            $this->manager->persist($entity);
            $this->addReference(sprintf('%s_%d', $groupName, $i), $entity);
        }
    }

    /**
     * Returns all entities according to the group name created earlier with the createMany function
     *
     * $this->getReferences('main_user');
     *
     * @param string $groupName
     *
     * @return object[]
     * @throws \InvalidArgumentException when the desired group name does not exist
     */
    protected function getReferences(string $groupName): array
    {
        if (!isset($this->referencesIndex[$groupName])) {
            $this->referencesIndex[$groupName] = [];
            foreach ($this->referenceRepository->getReferences() as $key => $ref) {
                if (str_starts_with($groupName.'_', $key)) {
                    $reference = $this->getReference($key);
                    $this->referencesIndex[$groupName][] = $reference;
                }
            }
        }
        if (empty($this->referencesIndex[$groupName])) {
            throw new \InvalidArgumentException(
                sprintf('Did not find any references saved with the group name "%s"', $groupName)
            );
        }

        return $this->referencesIndex[$groupName];
    }

    /**
     * Returns a randomly generated date between two given dates
     *
     * @param \DateTimeInterface $startDate
     * @param \DateTimeInterface $endDate
     *
     * @return \DateTimeInterface
     */
    protected function randomDateBetween(\DateTimeInterface $startDate, \DateTimeInterface $endDate): \DateTimeInterface
    {
        $min = strtotime($startDate->format('Y-m-d'));
        $max = strtotime($endDate->format('Y-m-d'));

        return new \DateTime(date('Y-m-d H:i:s', random_int($min, $max)));
    }
}
