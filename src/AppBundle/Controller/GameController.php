<?php

namespace AppBundle\Controller;

use AppBundle\Domain\Entity\Game\Game;
use AppBundle\Domain\Entity\Player\ApiPlayer;
use AppBundle\Domain\Service\MazeRender\MazeHtmlRender;
use AppBundle\Form\CreateGame\GameEntity;
use AppBundle\Form\CreateGame\GameForm;
use AppBundle\Form\CreateGame\PlayerEntity;
use AppBundle\Service\GameEngine\GameEngineDaemon;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * Create new game
     *
     * @Route("/create", name="game_create")
     * @param Request $request
     * @return Response
     */
    public function createAction(Request $request)
    {
        // Create game data entity
        $gameEntity = new GameEntity();

        // Create the game data form (step 1)
        $form = $this->createForm('\AppBundle\Form\CreateGame\GameForm', $gameEntity, array(
            'action'    => $this->generateUrl('game_create'),
            'form_type' => GameForm::TYPE_GAME_DATA
        ));

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Add players to the game data entity
            for ($i = 0; $i < $gameEntity->getPlayerNum(); ++$i) {
                $gameEntity->addPlayer(new PlayerEntity());
            }

            // Create the players form (step 2)
            $form = $this->createForm('\AppBundle\Form\CreateGame\GameForm', $gameEntity, array(
                'action'    => $this->generateUrl('game_create_next'),
                'form_type' => GameForm::TYPE_PLAYERS
            ));
        }

        return $this->render('game/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * Create new game
     *
     * @Route("/create/next", name="game_create_next")
     * @param Request $request
     * @return Response
     */
    public function createNextAction(Request $request)
    {
        // Create game data $players entity
        $gameEntity = new GameEntity();

        // Create the players form (step 2)
        $form = $this->createForm('\AppBundle\Form\CreateGame\GameForm', $gameEntity, array(
            'action'    => $this->generateUrl('game_create_next'),
            'form_type' => GameForm::TYPE_PLAYERS
        ));

        // Handle the request & if the data is valid...
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Create the maze of height x width
            $mazeBuilder = $this->get('app.maze.builder');
            $maze = $mazeBuilder->buildRandomMaze(
                $gameEntity->getHeight(),
                $gameEntity->getWidth()
            );

            // Create players
            $players = array();
            for ($pos = 0; $pos < $gameEntity->getPlayerNum(); $pos++) {
                $players[] = new ApiPlayer($gameEntity->getPlayerAt($pos)->getUrl(), $maze->start());
            }

            // Create game
            $game = new Game(
                $maze,
                $players,
                array(),
                $gameEntity->getGhostRate(),
                $gameEntity->getMinGhosts()
            );

            // Save game datra in the database
            $entity = new \AppBundle\Entity\Game($game);
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            // Show the game
            return $this->redirectToRoute('game_view', array(
                'uuid' => $game->uuid()
            ));
        }

        return $this->render('game/create.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * View Game
     *
     * @Route("/{uuid}/view",name="game_view",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function viewAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $renderer = new MazeHtmlRender();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:view.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * Panels Game
     *
     * @Route("/{uuid}/panels",name="game_panels",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function panelsAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $renderer = new MazeHtmlRender();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        return $this->render(':game:panels.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));
    }

    /**
     * View only maze
     *
     * @Route("/{uuid}/refresh",name="game_refresh",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return JsonResponse
     */
    public function refreshAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $renderer = new MazeHtmlRender();
        $game = $entity->toDomainEntity();
        $maze = $renderer->render($game);

        $html = $this->renderView(':game:maze.html.twig', array(
            'game' => $game,
            'maze' => $maze
        ));

        $data = array(
            'html' => $html,
            'playing' => $game->playing(),
            'finished' => $game->finished()
        );

        return new JsonResponse($data);
    }

    /**
     * Start a game
     *
     * @Route("/{uuid}/start",name="game_start",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function startAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $game = $entity->toDomainEntity();
        $game->startPlaying();

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * Stop a game
     *
     * @Route("/{uuid}/stop",name="game_stop",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function stopAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $game = $entity->toDomainEntity();
        $game->stopPlaying();

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * Reset a game
     *
     * @Route("/{uuid}/reset",name="game_reset",
     *     requirements={"uuid": "[0-9a-f]{8}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{4}\-[0-9a-f]{12}"})
     *
     * @param string $uuid
     * @return Response
     */
    public function resetAction($uuid)
    {
        $this->checkDaemon();

        /** @var \AppBundle\Entity\Game $entity */
        $entity = $this->getDoctrine()->getRepository('AppBundle:Game')->findOneBy(array(
            'uuid' => $uuid
        ));

        $game = $entity->toDomainEntity();
        $game->resetPlaying();

        $entity->fromDomainEntity($game);
        $em = $this->getDoctrine()->getManager();
        $em->persist($entity);
        $em->flush();

        return new Response();
    }

    /**
     * Checks if the daemon is running in the background
     *
     * @return void
     */
    private function checkDaemon()
    {
        /** @var GameEngineDaemon $daemon */
        $daemon = $this->get('app.game.engine.daemon');
        $daemon->start();
    }

    /**
     * Admin game daemon
     *
     * @Route("/admin", name="admin_view")
     * @return Response
     */
    public function adminAction()
    {
        /** @var GameEngineDaemon $daemon */
        $daemon = $this->get('app.game.engine.daemon');
        $processId = $daemon->getProcessId();

        return $this->render('game/admin.html.twig', array(
            'processId' => $processId
        ));
    }

    /**
     * Start athe daemon
     *
     * @Route("/admin/start",name="admin_start")
     * @return Response
     */
    public function startDaemonAction()
    {
        /** @var GameEngineDaemon $daemon */
        $daemon = $this->get('app.game.engine.daemon');
        $daemon->start();

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Start athe daemon
     *
     * @Route("/admin/stop",name="admin_stop")
     * @return Response
     */
    public function stopDaemonAction()
    {
        /** @var GameEngineDaemon $daemon */
        $daemon = $this->get('app.game.engine.daemon');
        $daemon->stop();

        return $this->redirectToRoute('admin_view');
    }

    /**
     * Start athe daemon
     *
     * @Route("/admin/restart",name="admin_restart")
     * @return Response
     */
    public function restartDaemonAction()
    {
        /** @var GameEngineDaemon $daemon */
        $daemon = $this->get('app.game.engine.daemon');

        $count = 0;
        do {
            $daemon->stop();
            usleep(100000);
        } while ($daemon->isRunning() || ++$count > 100);

        $daemon->start();

        return $this->redirectToRoute('admin_view');
    }
}
