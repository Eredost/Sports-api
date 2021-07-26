<?php

namespace App\Tests\Entity;

use App\DataFixtures\Providers\UserProvider;
use App\Entity\User;
use App\Tests\Entity\Traits\AssertTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UserTest extends KernelTestCase
{
    use AssertTrait;

    protected function getEntity(): User
    {
        return (new User())
            ->setUsername('JohnDoe')
            ->setRoles(['ROLE_USER'])
            ->setPlainPassword('Pas3word#')
        ;
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidBlankUsername(): void
    {
        $this->assertHasErrors($this->getEntity()->setUsername(''), 2);
    }

    public function testInvalidLengthUsername(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setUsername('Mi'), 1);
        $this->assertHasErrors($user->setUsername(str_repeat('*', 61)), 1);
    }

    public function testInvalidUniqueEmail(): void
    {
        $this->assertHasErrors($this->getEntity()->setUsername(UserProvider::getUsers()[0]['username']), 1);
    }

    public function testInvalidBlankRoles(): void
    {
        $this->assertHasErrors($this->getEntity()->setRoles(['']), 1);
    }

    public function testInvalidValueRoles(): void
    {
        $this->assertHasErrors($this->getEntity()->setRoles(['ROLE_USE', 'ROLE_MODERATOR']), 2);
    }

    public function testReturnTypeRoles(): void
    {
        self::assertIsArray($this->getEntity()->getRoles());
    }

    public function testInvalidBlankPlainPassword(): void
    {
        $this->assertHasErrors($this->getEntity()->setPlainPassword(''), 1);
    }

    public function testInvalidLengthPlainPassword(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setPlainPassword('123'), 1);
        $this->assertHasErrors($user->setPlainPassword(str_repeat('*', 41)), 1);
    }

    public function testInvalidValuesPlainPassword(): void
    {
        $user = $this->getEntity();

        $this->assertHasErrors($user->setPlainPassword('password'), 1);
        $this->assertHasErrors($user->setPlainPassword('Password'), 1);
        $this->assertHasErrors($user->setPlainPassword('pas3word'), 1);
        $this->assertHasErrors($user->setPlainPassword('P3363388'), 1);
    }

    public function testReturnTypeCreatedAt(): void
    {
        self::assertInstanceOf(\DateTimeInterface::class, $this->getEntity()->getCreatedAt());
    }

    public function testReturnTypeUpdatedAt(): void
    {
        self::assertNull($this->getEntity()->getUpdatedAt());
    }
}
