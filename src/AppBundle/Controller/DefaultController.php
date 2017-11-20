<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Default Controller
 *
 * @package AppBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     */
    public function indexAction()
    {
        $this->get('logger')->info('DefaultController::indexAction()');
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     * @return Response
     */
    public function rulesAction()
    {
        $this->get('logger')->info('DefaultController::rulesAction()');
        return $this->render('default/rules.html.twig');
    }

    /**
     * @Route("/credits", name="credits")
     * @return Response
     */
    public function creditsAction()
    {
        $this->get('logger')->info('DefaultController::creditsAction()');
        return $this->render('default/credits.html.twig');
    }
}
