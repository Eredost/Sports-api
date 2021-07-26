<?php

namespace App\Tests\Entity;

use App\Entity\Sport;
use App\Tests\Entity\Traits\AssertTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SportTest extends KernelTestCase
{
    use AssertTrait;

    protected function getEntity(): Sport
    {
        return (new Sport())
            ->setLabel('sportLabel')
        ;
    }

    public function testValidEntity(): void
    {
        $this->assertHasErrors($this->getEntity());
    }

    public function testInvalidBlankLabel(): void
    {
        $this->assertHasErrors($this->getEntity()->setLabel(''), 1);
    }

    public function testInvalidLengthLabel(): void
    {
        $this->assertHasErrors($this->getEntity()->setLabel(str_repeat('*', 61)), 1);
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
