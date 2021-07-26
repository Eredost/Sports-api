<?php

namespace App\Entity\Traits;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use OpenApi\Annotations as OA;

trait TimestampableTrait
{
    /**
     * @ORM\Column(type="datetime")
     * @Groups({"read"})
     *
     * @OA\Property(
     *     type = "string",
     *     format = "date-time",
     *     description = "The creation date of the resource"
     * )
     */
    private DateTimeInterface $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"read"})
     *
     * @OA\Property(
     *     type = "string",
     *     format = "date-time",
     *     nullable = true,
     *     description = "The date of the last update of the resource"
     * )
     */
    private ?DateTimeInterface $updatedAt = null;

    /**
     * @return DateTimeInterface|null
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeInterface $createdAt
     *
     * @return self
     */
    public function setCreatedAt(DateTimeInterface $createdAt):  self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface|null $updatedAt
     *
     * @return self
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }
}
