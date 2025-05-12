<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_USERNAME', fields: ['username'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(["active_game:read"])]
    private ?string $username = null;

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne(inversedBy: 'activeUsers')]
    private ?ActiveGames $activeGames = null;

    /**
     * @var Collection<int, ActiveCards>
     */
    #[ORM\OneToMany(targetEntity: ActiveCards::class, mappedBy: 'user', orphanRemoval: true)]
    private Collection $activeCards;

    public function __construct()
    {
        $this->activeCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getActiveGames(): ?ActiveGames
    {
        return $this->activeGames;
    }

    public function setActiveGames(?ActiveGames $activeGames): static
    {
        $this->activeGames = $activeGames;

        return $this;
    }

    /**
     * @return Collection<int, ActiveCards>
     */
    public function getActiveCards(): Collection
    {
        return $this->activeCards;
    }

    public function addActiveCard(ActiveCards $activeCard): static
    {
        if (!$this->activeCards->contains($activeCard)) {
            $this->activeCards->add($activeCard);
            $activeCard->setUser($this);
        }

        return $this;
    }

    public function removeActiveCard(ActiveCards $activeCard): static
    {
        if ($this->activeCards->removeElement($activeCard)) {
            // set the owning side to null (unless already changed)
            if ($activeCard->getUser() === $this) {
                $activeCard->setUser(null);
            }
        }

        return $this;
    }
}
