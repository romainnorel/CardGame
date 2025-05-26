<?php

namespace App\DataFixtures;

use App\Entity\SpellEffect;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Proxies\__CG__\App\Entity\Cards;
use Proxies\__CG__\App\Entity\Spells;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->generateCards($manager);

        $manager->flush();
    }

    private function generateCards(ObjectManager $manager): void
    {
        $paladinCard = $this->generateCardWithData($manager, "paladin.jpg", 5, 55);
        $bigAttack = $this->generateSpellForCardWithData($manager, $paladinCard, "bigAttack.png", "Inflige 10 dégats à l'ennemie en face", 2);
        $this->generateSpellEffectForSpellWithData($manager, $bigAttack, "damage", 10, "ennemy", 1);
        $solidHeal = $this->generateSpellForCardWithData($manager, $paladinCard, "solidHeal.png", "Soigne 10 pv à l'allié ciblé", 3);
        $this->generateSpellEffectForSpellWithData($manager, $solidHeal, "heal", 10, "ally", 1);

    }

    private function generateCardWithData(ObjectManager $manager, string $name, int $speed, int $hp): Cards
    {
        $card = new Cards();
        $card->setName($name);
        $card->setSpeed($speed);
        $card->setHp($hp);

        $manager->persist($card);

        return $card;
    }

    private function generateSpellForCardWithData(ObjectManager $manager, Cards $paladinCard, string $name, string $text, int $cooldown): Spells 
    {
        $spell = new Spells();
        $spell->setName($name);
        $spell->setText($text);
        $spell->setCard($paladinCard);
        $spell->setCooldown($cooldown);

        $manager->persist($spell);

        return $spell;
    }

    private function generateSpellEffectForSpellWithData(ObjectManager $manager, Spells $spell, string $type, int $value, string $target, int $nbOfTarget): SpellEffect 
    {
        $spellEffect = new SpellEffect();
        $spellEffect->setSpell($spell);
        $spellEffect->setType($type);
        $spellEffect->setValue($value);
        $spellEffect->setTarget($target);
        $spellEffect->setNumberOfTarget($nbOfTarget);

        $manager->persist($spellEffect);

        return $spellEffect;
    }
}
