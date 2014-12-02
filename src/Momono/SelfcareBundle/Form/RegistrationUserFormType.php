<?php
 
namespace Momono\SelfcareBundle\Form;
 
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
 
class RegistrationUserFormType extends RegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
 
        // add your custom field
        $builder->add('phoneNumber', null, ['required' => true]);
    }
 
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults(['translation_domain' => 'FOSUserBundle']);
    }
 
    public function getName()
    {
        return 'momono_user_registration_user';
    }
}