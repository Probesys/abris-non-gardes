<?php

namespace App\FormFilter;

use App\Entity\ListingValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingValueFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', null, ['label' => 'Generics.fields.name', 'required' => false])
                ->add('listingType', null, ['label' => 'Entities.ListingValue.fields.listingType', 'required' => false, 'attr' => ['class' => 'select2']])
                ->add('parent', null, ['label' => 'Entities.ListingValue.fields.listingParent', 'required' => false, 'attr' => ['class' => 'select2']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListingValue::class,
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
