<?php
namespace Momono\SelfcareBundle\Form;
 
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
 
class LoginType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('username', 'email');
        $builder->add('password', 'password');
        
        $builder->add('rememberme', 'checkbox', array(
            'label'     => 'Remember me',
            'required'  => false,
        ));
    }
    
    public function getName()
    {
        return 'selfcare_login';
    }
}