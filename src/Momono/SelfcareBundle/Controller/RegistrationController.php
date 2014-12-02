<?php
namespace Momono\SelfcareBundle\Controller;
 
use FOS\UserBundle\Controller\RegistrationController as BaseController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Momono\SelfcareBundle\Form\RegistrationUserFormType;
use Symfony\Component\HttpFoundation\Request;


class RegistrationController extends BaseController
{
    public function registerUserAction(Request $request)
    { 
        return parent::registerAction($request);
    }
    /** 
     * registerUserAction
     * WARNING: This method is kind of a duplicate of the base FOS RegistationController,
     * but adds a ROLE_USRR and override the default handler to add our ow FormType
     * It also changes the redirect route
     *
     * @access public
     * @return void
     */
    public function oldregisterUserAction()
    {   
        
        $class = $this->container->getParameter('fos_user.model.user.class');
        /**
         * RegistrationUserFormType $form
         */
        $form = $this->container->get('form.factory');
        $form->create(new RegistrationUserFormType($class));

        $formHandler = $this->getFormHandler($form);
 
        $process = $formHandler->process(false);
 
        if ($process) {
            $user = $form->getData();
            $user->addRole('ROLE_USER');
 
            $route = 'user_dashboard_index';
 
            $this->setFlash('fos_user_success', 'registration.flash.user_reted');
            $url = $this->container->get('router')->generate($route);
            $response = new RedirectResponse($url);
 
            $this->authenticateUser($user, $response);
 
            return $response;
        }
 
        $params = ['form' => $form->createView()];
 
        return $this->container
            ->get('templating')
            ->renderResponse('MomonoSelfcareBundle:Registration:register.html.twig', $params);
    }
 
    /** 
     * getFormHandler
     *
     * @param mixed $form
     * @access protected
     * @return RegistrationFormHandler
     *
    protected function getFormHandler($form)                                                                                                       
    {   
        
        return new \FOS\UserBundle\Form\RegistrationFormHandler(
            $form,
            $this->container->get('request'),
            $this->container->get('fos_user.user_manager'),
            $this->container->get('fos_user.mailer'),
            $this->container->get('fos_user.util.token_generator')
        );  
    } */
}
