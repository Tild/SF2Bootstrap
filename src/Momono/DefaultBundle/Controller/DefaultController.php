<?php

namespace Momono\DefaultBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MomonoDefaultBundle:Default:index.html.twig', array('name' => $name));
    }
}
