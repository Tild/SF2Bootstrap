<?php

namespace Momono\SelfcareBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Momono\SelfcareBundle\Form\LoginType;
use Symfony\Component\Security\Core\SecurityContext;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('MomonoSelfcareBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        $form = $this->createForm(new LoginType());
        
        $form->handleRequest($request);

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        return $this->render(
            'MomonoSelfcareBundle:Default:login.html.twig',
            array(
                // last username entered by the user
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
                'form' => $form->createView(),
            )
        );
    }
}
