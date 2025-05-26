<?php

namespace App\Controller;

use App\Entity\ActiveGames;
use App\Service\SpellEffectService;
use App\Service\TurnService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\LinkSetterService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    private EntityManagerInterface $em;
    private SerializerInterface $serializer;
    private LinkSetterService $linkSetterService;
    private SpellEffectService $spellEffectService;
    private TurnService $turnService;

    public function __construct(EntityManagerInterface $em, SerializerInterface $serializer, LinkSetterService $linkSetterService, SpellEffectService $spellEffectService, TurnService $turnService)
    {
        $this->em = $em;
        $this->serializer = $serializer;
        $this->linkSetterService = $linkSetterService;
        $this->spellEffectService = $spellEffectService;
        $this->turnService = $turnService;
    }

    #[Route('/api/activeGame', methods: ['GET'])]
    public function getActiveGame(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('JWT token is invalid or expired');
        }

        $activeGame = $this->em->getRepository(ActiveGames::class)->findByUserId( $user->getId());

        if (!$activeGame) {
            throw $this->createNotFoundException(
                'No game found for user id '. $user->getId()
            );
        }

        $jsonData = $this->serializer->serialize($this->linkSetterService->setLinkForActiveGame($activeGame), 'json', ['groups' => 'active_game:read']);

        return new JsonResponse($jsonData, Response::HTTP_OK, [], true);
    }

    #[Route('/api/use-spell', methods: ['POST'])]
    public function useSpell(Request $request): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('JWT token is invalid or expired');
        }

        $activeGame = $this->em->getRepository(ActiveGames::class)->findByUserId( $user->getId());

        $parameters = json_decode($request->getContent(), true);

        // $this->spellEffectService->checkIfSpellLegal($activeGame, $parameters);
        $this->spellEffectService->resolveSpellEffect($activeGame, $parameters);

        return new JsonResponse('OK', Response::HTTP_OK, [], true);
    }

    #[Route('/api/activeGame/end-turn', methods: ['GET'])]
    public function endTurn(): JsonResponse
    {
        $user = $this->getUser();

        if (!$user) {
            throw new UnauthorizedHttpException('JWT token is invalid or expired');
        }

        $activeGame = $this->em->getRepository(ActiveGames::class)->findByUserId( $user->getId());

        if (!$activeGame) {
            throw $this->createNotFoundException(
                'No game found for user id '. $user->getId()
            );
        }

        return $this->turnService->resolveEndTurn($activeGame);
    }
}
