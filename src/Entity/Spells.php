<?php

namespace App\Entity;

use App\Repository\SpellsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SpellsRepository::class)]
class Spells
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["active_game:read"])]
    private ?string $text = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $link = null;

    #[ORM\ManyToOne(inversedBy: 'spells')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cards $card = null;

    /**
     * @var Collection<int, SpellEffect>
     */
    #[ORM\OneToMany(targetEntity: SpellEffect::class, mappedBy: 'spell', orphanRemoval: true)]
    #[Groups(["active_game:read"])]
    private Collection $spellEffects;

    public function __construct()
    {
        $this->spellEffects = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
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

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): static
    {
        $this->text = $text;

        return $this;
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
     * @return Collection<int, SpellEffect>
     */
    public function getSpellEffects(): Collection
    {
        return $this->spellEffects;
    }

    public function addSpellEffect(SpellEffect $spellEffect): static
    {
        if (!$this->spellEffects->contains($spellEffect)) {
            $this->spellEffects->add($spellEffect);
            $spellEffect->setSpell($this);
        }

        return $this;
    }

    public function removeSpellEffect(SpellEffect $spellEffect): static
    {
        if ($this->spellEffects->removeElement($spellEffect)) {
            // set the owning side to null (unless already changed)
            if ($spellEffect->getSpell() === $this) {
                $spellEffect->setSpell(null);
            }
        }

        return $this;
    }
}
