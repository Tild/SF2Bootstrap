<?php

namespace Momono\DefaultBundle\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;

class UserManager
{
    /**
     * @var Symfony\Component\Security\Core\Encoder\EncoderFactory 
     */
    private $encoderFactory;
    
    /**
     * @var array 
     */
    private $rolesHierarchy;
    
    /**
     * Constructor
     * 
     * @param EncoderFactory $encoderFactory
     */
    public function __construct(EncoderFactory $encoderFactory, $roles)
    {
        $this->encoderFactory = $encoderFactory;
        $this->rolesHierarchy = $roles;
    }
    
    /**
     * 
     * @param UserInterface $user
     * @param string $plaintextPassword
     */
    public function setUserPassword(UserInterface $user, $plaintextPassword)
    {
        $hash = $this->encoderFactory->getEncoder($user)->encodePassword($plaintextPassword, $user->getSalt());
        $user->setPassword($hash);
    }
    
    public function getAcceptedRoles()
    {
        $roles = array();

        array_walk_recursive($this->rolesHierarchy, function($val) use (&$roles) {
            $roles[$val] = $val;
        });

        return array_unique($roles);
    }
}