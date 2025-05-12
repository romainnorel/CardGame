<?php

namespace App\Service;

use App\Entity\ActiveCards;
use App\Entity\ActiveGames;
use App\Entity\Spells;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class LinkSetterService
{
    private ParameterBagInterface $params;
    private RequestStack $requestStack;

    public function __construct(ParameterBagInterface $params, RequestStack $requestStack)
    {
        $this->params = $params; 
        $this->requestStack = $requestStack;
    }

    public function setLinkForActiveCard(ActiveCards $activeCard): ActiveCards 
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return $activeCard;
        }

        $card = $activeCard->getCard();
        $card->setLink($request->getSchemeAndHttpHost() . $this->params->get('upload_dir') . 'cards/' . $card->getName());

        $spells = $activeCard->getActiveSpells();

        foreach ($spells as $spell) {
            $this->setLinkForSpell($spell);
        }

        $activeCard->setCard($card);
        $activeCard->setActiveSpells($spells);

        return $activeCard;
    }

    public function setLinkForActiveGame(ActiveGames $activeGame): ActiveGames 
    {
        $activeCards = $activeGame->getActiveCards();

        foreach ($activeCards as $activecard) {
            $this->setLinkForActiveCard($activecard);
        }

        $activeGame->setActiveCards($activeCards);
        
        return $activeGame;
    }

    public function setLinkForSpell(Spells $spell): Spells 
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$request) {
            return $spell;
        }

        return $spell->setLink($request->getSchemeAndHttpHost() . $this->params->get('upload_dir') . 'spells/' . $spell->getName());
    }
}