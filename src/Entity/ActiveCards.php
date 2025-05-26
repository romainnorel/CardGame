<?php

namespace App\Entity;

use App\Repository\ActiveCardsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActiveCardsRepository::class)]
class ActiveCards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activeCards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["active_game:read"])]
    private ?Cards $card = null;

    #[ORM\ManyToOne(inversedBy: 'activeCards')]
    #[ORM\JoinColumn(nullable: false)]
    private ?ActiveGames $activeGame = null;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $currentHp = null;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $currentSpeed = null;

    #[ORM\Column]
    #[Groups(groups: ["active_game:read"])]
    private ?int $position = null;

    #[ORM\ManyToOne(inversedBy: 'activeCards')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["active_game:read"])]
    private ?User $user = null;

    /**
     * @var Collection<int, ActiveSpell>
     */
    #[ORM\OneToMany(targetEntity: ActiveSpell::class, mappedBy: 'activeCard')]
    #[Groups(groups: ["active_game:read"])]
    private Collection $activeSpells;

    public function __construct()
    {
        $this->activeCard = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCard(): ?Cards
    {
        return $this->card;
    }

    public function setCard(?Cards $card): static
    {
        $this->card = $card;

        return $this;
    }

    public function getActiveGame(): ?ActiveGames
    {
        return $this->activeGame;
    }

    public function setActiveGame(?ActiveGames $activeGame): static
    {
        $this->activeGame = $activeGame;

        return $this;
    }

    public function getCurrentHp(): ?int
    {
        return $this->currentHp;
    }

    public function setCurrentHp(int $currentHp): static
    {
        $this->currentHp = $currentHp;

        return $this;
    }

    
    public function getCurrentSpeed(): ?int
    {
        return $this->currentSpeed;
    }

    public function setCurrentSpeed(int $currentSpeed): static
    {
        $this->currentSpeed = $currentSpeed;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, ActiveSpell>
     */
    public function getActiveSpells(): Collection
    {
        return $this->activeSpells;
    }

    public function addActiveSpell(ActiveSpell $activeSpell): static
    {
        if (!$this->activeSpells->contains($activeSpell)) {
            $this->activeSpells->add($activeSpell);
            $activeSpell->setActiveCard($this);
        }

        return $this;
    }

    public function removeActiveSpell(ActiveSpell $activeSpell): static
    {
        if ($this->activeSpells->removeElement($activeSpell)) {
            // set the owning side to null (unless already changed)
            if ($activeSpell->getActiveCard() === $this) {
                $activeSpell->setActiveCard(null);
            }
        }

        return $this;
    }

       public function setActiveSpells(?Collection $activeSpells): static
    {
        $this->activeSpells = $activeSpells;

        return $this;
    }

}
