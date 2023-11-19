<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints\PasswordStrength;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'Email.Unique')]
#[UniqueEntity(fields: ['username'], message: 'Username.Unique')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * The email address of the entity, or null if not set
     *
     * @var string|null
     */
    #[ORM\Column(name: 'email', type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Email]
    private ?string $email = null;

    /**
     * The role of the User empty but will be set on ROLE USER at registration
     *
     * @var array
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(name: 'password', type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 8)]
    #[
        Assert\PasswordStrength([
            'minScore'  => PasswordStrength::STRENGTH_MEDIUM,
            'message'   => 'Password.Tooweak',
        ])
    ]
    // BEWARE ALWAYS PUT COMPLEXITY FROM BEGINNING : ex: 5s0inNQ0bLrF4Ijq
    //#[Assert\] Complexité avec REgex pour Carectère spéciaux et + chiffre + Majuscule
    private ?string $password = null;

    /**
     * Relation with Trick the user creates.
     *
     * @var Collection
     */
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Trick::class)]
    private Collection $tricks;


    /**
     * Comment Relation can be added here if needed
     * #[ORM\OneToMany(mappedBy: 'author', targetEntity: Comment::class)]
     * private Collection $comments;
     *
     */

    /**
     * Username can't be blank and must be unique. At list 3 caracters to make one.
     *
     * @var string|null
     */
    #[ORM\Column(name: 'username', type: 'string', length: 255, unique: true)]
    #[Assert\NotBlank]
    #[Assert\Length(min: 3)]
    private ?string $username = null;

    /**
     * IsVerified indicates that the users has checked his mail box to confirm registration
     *
     * @var boolean
     */
    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    /**
     * Uuid is unique and can be called to identify the object. Using uuid V6.
     *
     * @var Uuid|null
     */
    #[ORM\Column(type: 'uuid')]
    private ?Uuid $uuid = null;

    /**
     * Token validator to veirfy identity of the person registrating
     *
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenValidator = null;

    /**
     * Token Reset is to verify identity of person reseting password
     *
     * @var string|null
     */
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tokenReset = null;

    /**
     * Profil pic link to the user
     *
     * @var ProfilPic|null
     */
    #[ORM\ManyToOne]
    private ?ProfilPic $profilPicId = null;

    public function __construct()
    {
        $this->tricks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // Guarantee every user at least has ROLE_USER.
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Trick>
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks->add($trick);
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getUuid(): ?Uuid
    {
        return $this->uuid;
    }

    public function setUuid(Uuid $uuid): static
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getTokenValidator(): ?string
    {
        return $this->tokenValidator;
    }

    public function setTokenValidator(?string $tokenValidator): static
    {
        $this->tokenValidator = $tokenValidator;

        return $this;
    }

    public function getTokenReset(): ?string
    {
        return $this->tokenReset;
    }

    public function setTokenReset(?string $tokenReset): static
    {
        $this->tokenReset = $tokenReset;

        return $this;
    }

    public function getProfilPicId(): ?ProfilPic
    {
        return $this->profilPicId;
    }

    public function setProfilPicId(?ProfilPic $profilPicId): static
    {
        $this->profilPicId = $profilPicId;

        return $this;
    }
}
