<?php

namespace App\Entity;

use App\Repository\ActiveGamesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActiveGamesRepository::class)]
class ActiveGames
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'activeGames')]
    private Collection $activeUsers;

    /**
     * @var Collection<int, ActiveCards>
     */
    #[ORM\OneToMany(targetEntity: ActiveCards::class, mappedBy: 'activeGame', orphanRemoval: true)]
    #[Groups(["active_game:read"])]
    private Collection $activeCards;

    public function __construct()
    {
        $this->activePlayers = new ArrayCollection();
        $this->activeCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getActiveUsers(): Collection
    {
        return $this->activeUsers;
    }

    public function addActiveUser(User $activeUser): static
    {
        if (!$this->activeUsers->contains($activeUser)) {
            $this->activeUsers->add($activeUser);
            $activeUser->setActiveGames($this);
        }

        return $this;
    }

    public function removeActiveUser(User $activeUser): static
    {
        if ($this->activeUsers->removeElement($activeUser)) {
            // set the owning side to null (unless already changed)
            if ($activeUser->getActiveGames() === $this) {
                $activeUser->setActiveGames(null);
            }
        }

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
            $activeCard->setActiveGame($this);
        }

        return $this;
    }

    public function removeActiveCard(ActiveCards $activeCard): static
    {
        if ($this->activeCards->removeElement($activeCard)) {
            // set the owning side to null (unless already changed)
            if ($activeCard->getActiveGame() === $this) {
                $activeCard->setActiveGame(null);
            }
        }

        return $this;
    }

    public function setActiveCards(Collection $activeCards): static
    {
        $this->activeCards = $activeCards;

        return $this;
    }
}
