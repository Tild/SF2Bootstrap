<?php

namespace Momono\FrontBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MomonoFrontBundle:Default:index.html.twig');
    }
}
