<?php

namespace App\Service;
use App\Entity\ActiveGames;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TurnService
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function resolveEndTurn(ActiveGames $activeGame): JsonResponse 
    {
        $this->reduceAllTimers($activeGame);

        return $this->checkIfGameIsOver($activeGame);
    }

    private function reduceAllTimers(ActiveGames $activeGame): void
    {
        foreach($activeGame->getActiveCards() as $activeCard) {
            foreach($activeCard->getActiveSpells() as $activeSpell) {
                if($activeSpell->getCurrentCooldown() > 0) {
                    $activeSpell->setCurrentCooldown($activeSpell->getCurrentCooldown() - 1);
                }
            }
            // Same for debuffs buff and dots
        }
    }

    private function checkIfGameIsOver(ActiveGames $activeGame): JsonResponse {
        foreach($activeGame->getActiveUsers() as $user) {
            $deadCount = 0;
            foreach($activeGame->getActiveCards() as $activeCard) {
                if($activeCard->getCurrentHp() === 0 && $activeCard->getUser() === $user) {
                    $deadCount += 1;
                }
            }
            if($deadCount === 4) {
                return new JsonResponse([
                    'isGameOver' => true,
                    'user' => [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(), 
                    ],
                ]);
            }
        }
        return new JsonResponse([
            'isGameOver' => false,
            'user' => null,
        ]);
    }
}