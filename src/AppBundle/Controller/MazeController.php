<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Service\MazeBuilder\MazeBuilderRecursiveDivision;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MazeController
 *
 * @package AppBundle\Controller
 * @Route("/maze")
 */
class MazeController extends Controller
{
    /**
     * Create random test game
     *
     * @Route("/create/random", name="game_create_random")
     * @return Response
     */
    public function createRandomAction()
    {
        $builder = new MazeBuilderRecursiveDivision();
        $maze = $builder->buildRandomMaze(80, 20);

        $player = new ApiPlayer('http://localhost/api');

        $game = new Game($maze, [$player]);

        $entity = new \AppBundle\Entity\Game($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return $this->redirectToRoute('game_view', [
            'uuid' => $game->uuid()
        ]);
    }

    /**
     * View Game
     *
     * @Route("/view/{uuid}", name="game_view")
     * @param string $uuid
     * @return Response
     */
    public function viewAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $game */
        $game = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy([
            'uuid' => $uuid
        ]);

        return $this->render(':maze:view.html.twig', [
            'game' => $game->toDomainEntity()
        ]);
    }
}
