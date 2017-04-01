<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Maze;
use AppBundle\Service\MazeBuilder\MazeBuilderRecursiveDivision;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Default Controller
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.root_dir').'/..').DIRECTORY_SEPARATOR,
        ]);
    }

    /**
     * @Route("/maze/test", name="maze-test")
     */
    public function mazeTestAction()
    {
        $builder = new MazeBuilderRecursiveDivision();
        $maze = $builder->buildRandomMaze(40, 20);

//        $entity = new Maze($maze);
//        $this->getDoctrine()->getManager()->persist($entity);
//        $this->getDoctrine()->getManager()->flush();

        return $this->render('default/maze-test.html.twig', [
            'maze' => $maze
        ]);
    }
}
