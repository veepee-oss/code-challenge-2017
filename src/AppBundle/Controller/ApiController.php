<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ApiController
 *
 * @package AppBundle\Controller
 * @Route("/api")
 */
class ApiController extends Controller
{
    const NAME = 'Dominator API';

    /**
     * Return the name of the API
     *
     * @Route("/", name="api_home")
     * @return JsonResponse
     */
    public function indexAction()
    {
        return new JsonResponse(static::NAME);
    }

    /**
     * Move the player
     *
     * @Route("/move", name="api_move")
     * @param Request $request
     * @return JsonResponse
     */
    public function moveAction(Request $request)
    {
        $move = rand(0, 3);
        switch ($move) {
            case 0: $move = 'UP';    break;
            case 1: $move = 'DOWN';  break;
            case 2: $move = 'LEFT';  break;
            case 3: $move = 'RIGHT'; break;
        }

        $result = array(
            'name' => static::NAME,
            'move' => $move
        );

        return new JsonResponse($result);
    }
}
