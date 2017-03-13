<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\DependencyInjection\Container;

class RegistrationType extends AbstractType
{
    private $routeName;

    /**
     * @param The User class name
     */
    public function __construct(Container $container)
    {
        $request = $container->get('request_stack')->getCurrentRequest();
        $this->routeName = $request->get('_route');
    }


    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        if ($this->routeName == 'api_post_users') {
            $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'csrf_protection' => false,
            ));
        } else {
            $resolver->setDefaults(array(
                'data_class' => 'AppBundle\Entity\User',
                'csrf_token_id' => 'registration',
                // BC for SF < 2.8
                'intention'  => 'registration',
            ));
        }
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';

        // Or for Symfony < 2.8
        // return 'fos_user_registration';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
