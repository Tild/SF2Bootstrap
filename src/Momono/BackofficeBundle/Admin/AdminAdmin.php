<?php

namespace Momono\BackofficeBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

use FOS\UserBundle\Model\UserManagerInterface;

class AdminAdmin extends Admin
{
    public $supportsPreviewMode = true;
    private $roles;

    /**
     * @param string $code
     * @param string $class
     * @param string $baseControllerName
     * @param string $rolesHierarchy
     */
    public function __construct($code, $class, $baseControllerName, $rolesHierarchy)
    {
        $roles = array();

        array_walk_recursive($rolesHierarchy, function($val) use (&$roles) {
            $roles[] = $val;
        });

        $this->roles = array_unique($roles);

        parent::__construct($code, $class, $baseControllerName);
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('username')
            ->add('email')
            ->add('enabled')
            ->add('password')
            ->add('lastLogin')
            ->add('locked')
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
            ->add('username')

            ->add('email')

            ->add('enabled')
            ->add('locked')
            ->add('expired')
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
        $formMapper
            ->with('General')
                ->add('username')
                ->add('email')
                ->add('plainPassword', 'text')
            ->end()
            ->with('Groups')
                //->add('groups', 'sonata_type_model', array('required' => false))
            ->end()
            ->with('Management')
                ->add('roles', 'choice', array( 'multiple' => true, 'choices' => $this->roles))
                ->add('locked', null, array('required' => false))
                ->add('expired', null, array('required' => false))
                ->add('enabled', null, array('required' => false))
                ->add('credentialsExpired', null, array('required' => false))
            ->end()
            
        ;
    }

    public function preUpdate($user)
    {
        $this->getUserManager()->updateCanonicalFields($user);
        $this->getUserManager()->updatePassword($user);
    }

    /**
     * @param UserManagerInterface $userManager
     */
    public function setUserManager(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return UserManagerInterface
     */
    public function getUserManager()
    {
        return $this->userManager;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('email')
            ->add('roles')
            ->add('enabled')
            ->add('lastLogin')
            ->add('locked')
            ->add('expired')
            ->add('expiresAt')
            ->add('credentialsExpired')
            ->add('credentialsExpireAt')

        ;
    }
}
