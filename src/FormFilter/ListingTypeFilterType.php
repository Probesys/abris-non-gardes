<?php

namespace App\FormFilter;

use App\Entity\ListingType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingTypeFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, ['label' => 'Generics.fields.name', 'required' => false])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => ListingType::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'listing_value_filter';
    }
}
