<?php

namespace Momono\SelfcareBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MomonoSelfcareBundle:Default:index.html.twig', array('name' => $name));
    }
}
