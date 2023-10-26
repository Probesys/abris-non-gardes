<?php

namespace App\Form;

use App\Entity\Territory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TerritoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null, ['label' => 'Generics.fields.name'])
            ->add('parent', null, ['label' => 'Entities.Territory.fields.parent', 'attr' => ['class' => 'territory-select2-autocomplete']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Territory::class,
        ]);
    }
}
