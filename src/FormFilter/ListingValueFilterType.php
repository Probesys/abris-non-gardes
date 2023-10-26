<?php

namespace App\FormFilter;

use App\Entity\ListingType;
use App\Entity\ListingValue;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ListingValueFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', null, ['label' => 'Generics.fields.name', 'required' => false])
                ->add('listingType', EntityType::class, [
                    'label' => 'Entities.ListingValue.fields.listingType',
                    'class' => ListingType::class,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'placeholder' => '',
                    'attr' => ['class' => 'select2'],
                ])
                ->add('parent', EntityType::class, [
                    'label' => 'Entities.ListingValue.fields.listingParent',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => false,
                    'required' => false,
                    'placeholder' => '',
                    'attr' => ['class' => 'select2'],
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
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
