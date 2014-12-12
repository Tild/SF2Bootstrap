<?php

namespace Momono\BackofficeBundle\Admin;

use Sonata\AdminBundle\Admin\Admin as SonataAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Momono\DefaultBundle\Service\UserManager;

class AdminAdmin extends SonataAdmin
{
    public $supportsPreviewMode = true;
    private $roles;
    
    /**
     *
     * @var Momono\DefaultBundle\Service\UserManager 
     */
    private $userManager;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param string $rolesHierarchy
     */
    public function __construct($code, $class, $baseControllerName, $rolesHierarchy, UserManager $userManager)
    {
        $roles = array();

        array_walk_recursive($rolesHierarchy, function($val) use (&$roles) {
            $roles[$val] = $val;
        });

        $this->roles = array_unique($roles);
        
        $this->userManager = $userManager;

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('email')
            ->add('enabled')
            ->add('password')
            ->add('roles')
            ->add('id')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('email')
            ->add('enabled')
            ->add('roles')
            ->add('_action', 'actions', array(
                'actions' => array(
                    'show' => array(),
                    'edit' => array(),
                    'delete' => array(),
                )
            ))
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $userRoles = $this->userManager->getAcceptedRoles();
        
        $formMapper
            ->with('General')
                ->add('email')
                ->add('plainPassword', 'text')
            ->end()
            ->with('Groups')
                //->add('groups', 'sonata_type_model', array('required' => false))
            ->end()
            ->with('Management')
                ->add('roles', 'choice', array( 'required' => false, 'multiple' => true, 'choices' => $userRoles))
                ->add('enabled', null, array('required' => false))
            ->end()
            
        ;
    }

    public function prePersist($user)
    {    
        $user->setSalt(md5(uniqid()));
        $this->updateAdmin($user);
    }
    
    public function preUpdate($user)
    {
        $this->updateAdmin($user);
    }
    
    public function updateAdmin($user)
    {
        $user->setUsername($user->getEmail());
        $password = trim($user->getPlainPassword());
        if (!empty($password)) {
            $this->userManager->setUserPassword($user, $password);

            //$encoder = $this->encoder->getEncoder($user);
            //$user->setPassword($encoder->encodePassword($password, $user->getSalt()));
        }
        $user->setPlainPassword('');
        
        
    }
    

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('email')
            ->add('roles')
            ->add('enabled')

        ;
    }
    
    public function validate(ErrorElement $errorElement, $object)
    {
        
    }
}
