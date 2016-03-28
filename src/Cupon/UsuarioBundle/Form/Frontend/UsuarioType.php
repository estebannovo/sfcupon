<?php

namespace Cupon\UsuarioBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre')
            ->add('apellidos')
            ->add('email', 'email')
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'Las dos contraseñas deben coincidir',
                'first_options' => array('label' => 'Contraseña'),
                'second_options' => array('label' => 'Retpite Contraseña'),
                'required' => false
            ))
            ->add('direccion')
            ->add('permite_email', 'checkbox', array('required' => false))
            ->add('fechaNacimiento', 'birthday')
            ->add('dni')
            ->add('numero_tarjeta')
            ->add('ciudad')
            //->add('registrarme', 'submit')
        ;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
           'data_class' => 'Cupon\UsuarioBundle\Entity\Usuario',
            'error_mapping' => array(
                'mayorDeEdad' => 'fechaNacimiento'
            ),
        ));
    }
    
    public function getName() {
        return 'cupon_usuariobundle_usuariotype';
    }
    
}