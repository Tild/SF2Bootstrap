<?php
namespace Momono\SelfcareBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Momono\SelfcareBundle\Form\RegistrationUserFormType;
use Symfony\Component\HttpFoundation\Request;


class RegistrationController
{
    /** 
     * registerUserAction
     * WARNING: This method is kind of a duplicate of the base FOS RegistationController,
     * but adds a ROLE_USRR and override the default handler to add our ow FormType
     * It also changes the redirect route
     *
     * @access public
     * @return void
     */
    public function registerUserAction()
    {   
        /**
         * RegistrationUserFormType $form
         */
        
    }
}
