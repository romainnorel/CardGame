<?php

namespace App\Entity;

use App\Repository\CardsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CardsRepository::class)]
class Cards
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $link = null;

    /**
     * @var Collection<int, Spells>
     */
    #[ORM\OneToMany(targetEntity: Spells::class, mappedBy: 'card')]
    private Collection $spells;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $speed = null;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $hp = null;

    public function __construct()
    {
        $this->spells = new ArrayCollection();
        $this->activeCards = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): static
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return Collection<int, Spells>
     */
    public function getSpells(): Collection
    {
        return $this->spells;
    }

  
    public function setSpells(Collection $spells): static
    {
        $this->spells = $spells;

        return $this;
    }

    public function addSpells(Spells $spell): static
    {
        if (!$this->spells->contains($spell)) {
            $this->spells->add($spell);
            $spell->setCards($this);
        }

        return $this;
    }

    public function removeSpells(Spells $spell): static
    {
        if ($this->spells->removeElement($spell)) {
            // set the owning side to null (unless already changed)
            if ($spell->getCards() === $this) {
                $spell->setCards(null);
            }
        }

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getHp(): ?int
    {
        return $this->hp;
    }

    public function setHp(int $hp): static
    {
        $this->hp = $hp;

        return $this;
    }
}
