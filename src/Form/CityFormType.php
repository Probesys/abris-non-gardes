<?php

namespace App\Form;

use App\Entity\City;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use App\Entity\Territory;

class CityFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name', null, ['label' => 'Entities.City.fields.name'])
              ->add('insee', null, ['label' => 'Entities.City.fields.insee', 'required' => true])
               ->add('department', null, ['label' => 'Entities.City.fields.department', 'attr' => ['data-help' => 'ex: ISERE'], 'required' => true])
                ->add('country', null, ['label' => 'Entities.City.fields.country', 'required' => true])
                ->add('zipCode', null, ['label' => 'Entities.City.fields.zipCode', 'required' => true])
                ->add('territories', Select2EntityType::class, [
                    'multiple' => true,
                    'remote_route' => 'autocomplete_territory',
                    'class' => Territory::class,
                    'label' => 'Entities.City.fields.territories',
                    'primary_key' => 'id',
                    'text_property' => 'name',
                    'minimum_input_length' => 2,
                    'scroll' => true,
                    'page_limit' => 30,
                    'allow_clear' => true,
                    'delay' => 250,
                    'cache' => true,
                    'cache_timeout' => 60000, // if 'cache' is true
                    'language' => 'fr',
                    'placeholder' => 'Entities.Territory.actions.selectOneOrManyTerritories',
                    'by_reference' => false,
                  ])


        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => City::class,
        ]);
    }
}
