<?php

namespace Momono\SelfcareBundle\Security;

use FOS\UserBundle\Security\UserProvider as BaseUserProvider;
use Symfony\Component\Security\Core\User\UserInterface as SecurityUserInterface;

class UserProvider extends BaseUserProvider 
{

    /**
     * Constructor.
     *
     * @param UserManagerInterface $userManager
     */
    public function __construct($userManager)
    {
        parent::__construct($userManager);
    }

    /**
     * refreshUser.
     *
     * @param SecurityUserInterface $user
     */
    public function refreshUser(SecurityUserInterface $user)
    {

        if (null === $reloadedUser = $this->userManager->findUserBy(array('id' => $user->getId()))) {
            throw new UsernameNotFoundException(sprintf('User with ID "%d" could not be reloaded.', $user->getId()));
        }

        return $reloadedUser;
    }
}
