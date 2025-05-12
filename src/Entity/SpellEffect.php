<?php

namespace App\Entity;

use App\Repository\SpellEffectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: SpellEffectRepository::class)]
class SpellEffect
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $Type = null;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $value = null;

    #[ORM\Column(length: 255)]
    #[Groups(["active_game:read"])]
    private ?string $target = null;

    #[ORM\Column]
    #[Groups(["active_game:read"])]
    private ?int $numberOfTarget = null;

    #[ORM\ManyToOne(inversedBy: 'spellEffects')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Spells $spell = null;
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->Type;
    }

    public function setType(string $Type): static
    {
        $this->Type = $Type;

        return $this;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): static
    {
        $this->value = $value;

        return $this;
    }

    public function getTarget(): ?string
    {
        return $this->target;
    }

    public function setTarget(string $target): static
    {
        $this->target = $target;

        return $this;
    }

    public function getNumberOfTarget(): ?int
    {
        return $this->numberOfTarget;
    }

    public function setNumberOfTarget(int $numberOfTarget): static
    {
        $this->numberOfTarget = $numberOfTarget;

        return $this;
    }

    /**
     * @return Collection<int, Spells>
     */
    public function getSpell(): Collection
    {
        return $this->spell;
    }

    public function addSpell(Spells $spell): static
    {
        if (!$this->spell->contains($spell)) {
            $this->spell->add($spell);
            $spell->setSpellEffect($this);
        }

        return $this;
    }

    public function removeSpell(Spells $spell): static
    {
        if ($this->spell->removeElement($spell)) {
            // set the owning side to null (unless already changed)
            if ($spell->getSpellEffect() === $this) {
                $spell->setSpellEffect(null);
            }
        }

        return $this;
    }

    public function setSpell(?Spells $spell): static
    {
        $this->spell = $spell;

        return $this;
    }
}
