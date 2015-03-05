<?php
namespace Momono\BackofficeBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Momono\BackofficeBundle\Entity\Admin;

class CreateAdministratorCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    public function configure()
    {
        $this
                ->setDefinition(array(
                    new InputOption('email', '', InputOption::VALUE_REQUIRED, 'The email/username'),
                    new InputOption('password', '', InputOption::VALUE_REQUIRED, 'The password'),
                    new InputOption('role', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'the roles'),
                ))
                ->setName('backoffice:admin:new')
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
        $helper = $this->getHelper('question');

        $output->writeln(array(
            '',
            $this->getHelperSet()->get('formatter')->formatBlock('Create a new administrator', 'bg=blue;fg=white', true),
            '',
        ));

        $question = new Question('Please enter a full email address:', '');
        $adminEmail = $helper->ask($input, $output, $question);

        $question = new Question('Please enter a password:');
        $question->setHidden(true);
        $question->setHiddenFallback(false);
        $adminPassword = $helper->ask($input, $output, $question);

        $question = new ChoiceQuestion(
                'Please select roles:',
                $this->getContainer()->get('DefaultBundle.user_manager')->getAcceptedRoles()
                );
        $question->setMultiselect(true);
        $adminRoles = $helper->ask($input, $output, $question);

        $this->generate($output, $adminEmail, $adminPassword, $adminRoles);
    }

    /**
     * @param OutputInterface $output
     * @param string          $adminEmail
     * @param string          $adminPassword
     * @param array           $adminRoles    Roles of the new administrator
     *
     * @throws \RuntimeException
     */
    protected function generate(
            OutputInterface $output,
            $adminEmail,
            $adminPassword,
            array $adminRoles
            ) {
        if (empty($adminEmail)) {
            throw new \RuntimeException('The email must be provided.');
        }
        if (empty($adminPassword)) {
            throw new \RuntimeException('The password must be provided.');
        }

        if (empty($adminRoles)) {
            throw new \RuntimeException('Please define a role.');
        }

        $allRoles = $this->getContainer()->get('DefaultBundle.user_manager')->getAcceptedRoles();
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

        $this->getContainer()->get('DefaultBundle.user_manager')->setUserPassword($user, $adminPassword);

        $em = $this->getContainer()->get('doctrine')->getManager();
        $em->persist($user);
        $em->flush();

        $output->writeln('Generating a new admin user: <info>OK</info>');
    }

    /**
     * @return DialogHelper
     */
    protected function getDialog()
    {
        $dialog = $this->getHelperSet()->get('dialog');
        if (!$dialog || get_class($dialog) !== 'Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper') {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }
}
