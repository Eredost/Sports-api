<?php

namespace App\DataFixtures;

use App\DataFixtures\Providers\UserProvider;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends AbstractFixture
{
    private UserPasswordEncoderInterface $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    protected function loadData(ObjectManager $manager): void
    {
        $users = UserProvider::getUsers();

        $this->createMany(count($users), 'users', function ($i) use ($users) {
            $user = (new User())
                ->setUsername($users[$i]['username'])
                ->setRoles($users[$i]['roles'])
            ;
            $user->setPassword($this->encoder->encodePassword($user, $users[$i]['plainPassword']));

            return $user;
        });

        $manager->flush();
    }
}
