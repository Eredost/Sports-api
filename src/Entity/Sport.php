<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\SportRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use OpenApi\Annotations as OA;

/**
 * @ORM\Entity(repositoryClass=SportRepository::class)
 * @UniqueEntity(
 *     fields = "label",
 *     message = "This label is already used"
 * )
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
     * @Groups({"read", "edit"})
     *
     * @Assert\NotBlank(
     *     message = "The label cannot be blank"
     * )
     * @Assert\Length(
     *     max = 60,
     *     maxMessage = "The label length cannot exceed {{ limit }} characters"
     * )
     *
     * @OA\Property(description="The sport label")
     */
    private ?string $label;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @param string|null $label
     *
     * @return Sport
     */
    public function setLabel(?string $label): Sport
    {
        $this->label = $label;

        return $this;
    }
}
