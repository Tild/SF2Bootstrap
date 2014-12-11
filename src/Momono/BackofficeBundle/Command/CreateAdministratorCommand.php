<?php
namespace Momono\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use Momono\BackofficeBundle\Entity\Admin;

use Symfony\Component\Security\Core\Role\RoleHierarchy;

class CreateAdministratorCommand extends ContainerAwareCommand {

    /**
     * {@inheritdoc}
     */
    public function configure() {
        $this
                ->setDefinition(array(
                    new InputOption('email', '', InputOption::VALUE_REQUIRED, 'The email/username'),
                    new InputOption('password', '', InputOption::VALUE_REQUIRED, 'The password'),
                    new InputOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'the roles'),
                ))
                ->setName('backoffice:create')
                ->setDescription('Create a new administrator')
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output) 
    {
        if ($input->isInteractive()) {
            return 1;
        }
        
        $adminEmail = $input->getArgument('email');
        $adminPassword = $input->getArgument('password');
        $adminRoles = $input->getArgument('role');
        
        $this->generate($output, $adminEmail, $adminPassword, array($adminRoles));
    }

    /** 
     * {@inheritdoc}
     */
    public function interact(InputInterface $input, OutputInterface $output) 
    {
        $dialog = $this->getDialog();
        
        
        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock('Create a new administrator', 'bg=blue;fg=white', true),
            '',
        ));
        
        $adminEmail = $dialog->ask(
                $output, '<question>Please enter a full email address:</question>', ''
        );

        $adminPassword = $dialog->askHiddenResponse(
                $output, '<question>Please enter a password:</question>', true
        );
        
        $adminRoles = $dialog->select(
            $output,
            '<question>Please select roles:</question>',
            $this->getUserRoles(),
            0,
            false,
            'The role "%s" is undefined',
            true // active l'option multiselect
        );

        $this->generate($output, $adminEmail, $adminPassword, $adminRoles);
    }
    
    /**
     * 
     * @param OutputInterface $output
     * @param string $adminEmail
     * @param string $adminPassword
     * @param array $adminRoles Roles of the new administrator
     * @throws \RuntimeException
     */
    protected function generate(
            OutputInterface $output,
            $adminEmail,
            $adminPassword,
            array $adminRoles
            )
    {
        if (empty($adminEmail)) {
            throw new \RuntimeException('The email must be provided.');
        }
        if (empty($adminPassword)) {
            throw new \RuntimeException('The password must be provided.');
        }
        
        if (empty($adminRoles)) {
            throw new \RuntimeException('Please define a role.');
        }
        
        $allRoles = $this->getUserRoles();
        $roles = array();
        foreach ($adminRoles as $role) {
            if (empty($allRoles[$role])) {
                throw new \RuntimeException('unknown role.');
            }
            $roles[] = $allRoles[$role];
        }
        
        $user = new Admin();
        $user->setEmail($adminEmail);
        $user->setUsername($adminEmail);
        $user->setSalt(md5(uniqid()));
        $user->setEnabled(true);
        $user->setRoles($roles);
        $encoder = $this->getContainer()->get('security.encoder_factory')->getEncoder($user);
        $user->setPassword($encoder->encodePassword($adminPassword, $user->getSalt()));
        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();
        
        $output->writeln('Generating a new admin user: <info>OK</info>');
        
    }
    
    /**
     * @return DialogHelper
     */
    protected function getDialog() {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
    
    /**
     * 
     */
    private function getUserRoles() {
        $rolesHierarchy = $this->getContainer()->get('security.role_hierarchy');
        $roles = array();
        array_walk_recursive($rolesHierarchy, function($val) use (&$roles) {
            $roles[] = $val;
        });
        return array_unique($roles);
    }

}
