<?php

namespace AppBundle\Controller;

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
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/rules", name="rules")
     */
    public function rulesAction()
    {
        echo 'RULES page';
        exit;
    }

    /**
     * @Route("/credits", name="credits")
     */
    public function creditsAction()
    {
        return $this->render('default/credits.html.twig');
    }
}
