<?php

namespace App\Service;

use App\Entity\ActiveCards;
use App\Entity\SpellEffect;
use App\Entity\Spells;
use Doctrine\ORM\EntityManagerInterface;
use Proxies\__CG__\App\Entity\ActiveGames;

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
        $spell = $this->em->getRepository(className: Spells::class)->findOneBy( ["id" => $parameters["spellId"]]);
        $targetActiveCard = $this->em->getRepository(className: ActiveCards::class)->findTargetedCard($activeGame->getId(), $parameters["targetCardPos"]);

        foreach($spell->getSpellEffects() as $spellEffect) {
            $this->handleSpellEffect($spellEffect, $targetActiveCard);
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