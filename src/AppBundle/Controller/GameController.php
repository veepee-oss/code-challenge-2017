<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Service\MazeBuilder\MazeBuilderRecursiveDivision;
use AppBundle\Domain\Service\MazeRender\MazeHtmlRender;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class GameController
 *
 * @package AppBundle\Controller
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * Create random test game
     *
     * @Route("/create/test", name="game_create_random")
     * @return Response
     */
    public function createRandomAction()
    {
        $builder = new MazeBuilderRecursiveDivision();
        $maze = $builder->buildRandomMaze(80, 20);

        $player1 = new ApiPlayer('http://localhost/web/app_dev.php/api/move', $maze->start());
        $player2 = new ApiPlayer('http://localhost/web/app_dev.php/api/move', $maze->start());

        $game = new Game($maze, [$player1, $player2]);

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
     * @Route("/view/{uuid}",name="game_view",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function viewAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy([
            'uuid' => $uuid
        ]);

        $renderer = new MazeHtmlRender();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:view.html.twig', [
            'game' => $game,
            'maze' => $maze
        ]);
    }

    /**
     * View only maze
     *
     * @Route("/view/{uuid}/render",name="game_render",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function renderAction($uuid)
    {
        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy([
            'uuid' => $uuid
        ]);

        $renderer = new MazeHtmlRender();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:maze.html.twig', [
            'maze' => $maze
        ]);
    }
}
