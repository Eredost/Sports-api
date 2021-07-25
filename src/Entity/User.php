<?php

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields = "username",
 *     message = "This username is already used"
 * )
 */
class User implements UserInterface
{
    public const ROLES = [
        'ROLE_USER',
    ];

    use TimestampableTrait;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=60, unique=true)
     *
     * @Assert\NotBlank(
     *     message = "The username cannot be blank"
     * )
     * @Assert\Length(
     *     min = 3,
     *     max = 60,
     *     minMessage = "The username should contain at least {{ limit }} characters",
     *     maxMessage = "The username length cannot exceed {{ limit }} characters"
     * )
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     *
     * @Assert\Type(
     *     type = "array",
     *     message = "The roles should be a valid array"
     * )
     * @Assert\All(
     *     @Assert\Choice(
     *         choices = User::ROLES,
     *         message = "You must provid a valid user role. Roles available: {{ choices }}"
     * )
     * )
     */
    private array $roles = [];

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @Assert\NotBlank(
     *     groups = {"create"},
     *     message = "The password cannot be blank"
     * )
     * @Assert\Regex(
     *     groups = {"create"},
     *     pattern = "/(?=^.{8,40}$)((?=.*\d)|(?=.*\W+))(?![.\n])(?=.*[A-Z])(?=.*[a-z]).*$/",
     *     message = "Your password must contain at least 8 characters and a maximum of 40 characters including one lower case, one upper case and one number"
     * )
     */
    private ?string $plainPassword = null;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     *
     * @return User
     */
    public function setUsername(string $username): User
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return User
     */
    public function setRoles(array $roles): User
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     *
     * @return User
     */
    public function setPassword(string $password): User
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }

    /**
     * @param string|null $plainPassword
     *
     * @return User
     */
    public function setPlainPassword(?string $plainPassword): User
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = null;
    }
}
