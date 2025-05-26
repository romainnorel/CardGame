<?php

namespace App\Service;

use App\Entity\ActiveCards;
use App\Entity\ActiveSpell;
use App\Entity\SpellEffect;
use App\Entity\ActiveGames;
use Doctrine\ORM\EntityManagerInterface;

class SpellEffectService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function checkIfSpellLegal(ActiveGames $activeGames, $parameters): bool {
        $isSpellLegal = false;
        $activeGames->getActiveCards();
        foreach($activeGames->getActiveCards() as $activeCard) {
            foreach($activeCard->getActiveSpells() as $spell) {
                if($spell.getId() == $parameters["spellId"]) {
                    $isSpellLegal = true;
                }
            }
        }

        return $isSpellLegal;
    }

    public function resolveSpellEffect(ActiveGames $activeGame, $parameters): void {
        $activeSpell = $this->em->getRepository(className: ActiveSpell::class)->findOneBy( ["id" => $parameters["activeSpellId"]]);
        $targetActiveCard = $this->em->getRepository(className: ActiveCards::class)->findTargetedCard($activeGame->getId(), $parameters["targetCardPos"]);

        foreach($activeSpell->getSpell()->getSpellEffects() as $spellEffect) {
            $this->handleSpellEffect($spellEffect, $targetActiveCard);
            $activeSpell->setCurrentCooldown($activeSpell->getSpell()->getCooldown());
        }

        $this->em->persist($targetActiveCard);
        $this->em->flush();
    }

    private function handleSpellEffect(SpellEffect $spellEffect, ActiveCards $targetActiveCard) {
        if($spellEffect->getType() === "damage") {
            $this->handleDamageSpell($spellEffect, $targetActiveCard);
        }
        if($spellEffect->getType() === "heal") {
            $this->handleHealSpell($spellEffect, $targetActiveCard);
        }
    }

    private function handleDamageSpell(SpellEffect $spellEffect, ActiveCards $targetActiveCard) {
        if ($targetActiveCard->getCurrentHp() - $spellEffect->getValue() <= 0) {
            $targetActiveCard->setCurrentHp(0);
        } else {
            $targetActiveCard->setCurrentHp(currentHp: $targetActiveCard->getCurrentHp() - $spellEffect->getValue());
        }
    }

    private function handleHealSpell(SpellEffect $spellEffect, ActiveCards $targetActiveCard) {
        $targetActiveCard->setCurrentHp(min(
            $targetActiveCard->getCurrentHp() + $spellEffect->getValue(),
            $targetActiveCard->getCard()->getHp()
        ));
    }
}