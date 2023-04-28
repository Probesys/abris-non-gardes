<?php

namespace App\Form;

use App\Entity\HelpMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpMessageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name', null, ['label' => 'Generics.fields.name'])
              ->add('message', null, ['label' => 'Generics.fields.helpMessage', 'attr' => ['class' => 'tinymce']])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => HelpMessage::class,
        ]);
    }
}
