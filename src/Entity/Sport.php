<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\SportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
 */
class Sport
{
    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"read"})
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     * @Groups({"read"})
     */
    private string $label;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getLabel(): ?string
    {
        return $this->label;
    }

    /**
     * @param string $label
     *
     * @return Sport
     */
    public function setLabel(string $label): Sport
    {
        $this->label = $label;

        return $this;
    }
}
