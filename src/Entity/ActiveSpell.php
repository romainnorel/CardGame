<?php

namespace App\Entity;

use App\Repository\ActiveSpellRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ActiveSpellRepository::class)]
class ActiveSpell
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(groups: ["active_game:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'activeSpells')]
    private ?ActiveCards $activeCard = null;

    #[ORM\Column]
    #[Groups(groups: ["active_game:read"])]
    private ?int $currentCooldown = null;

    #[ORM\ManyToOne(inversedBy: 'activeSpells')]
    #[Groups(groups: ["active_game:read"])]
    private ?Spells $spell = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getActiveCard(): ?ActiveCards
    {
        return $this->activeCard;
    }

    public function setActiveCard(?ActiveCards $activeCard): static
    {
        $this->activeCard = $activeCard;

        return $this;
    }

    public function getCurrentCooldown(): ?int
    {
        return $this->currentCooldown;
    }

    public function setCurrentCooldown(int $currentCooldown): static
    {
        $this->currentCooldown = $currentCooldown;

        return $this;
    }

    public function getSpell(): ?Spells
    {
        return $this->spell;
    }

    public function setSpell(?Spells $spell): static
    {
        $this->spell = $spell;

        return $this;
    }
}
