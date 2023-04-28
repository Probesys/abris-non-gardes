<?php

namespace App\Form;

use App\Entity\Coordinate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\Extension\Core\Type\TelType;
use App\Entity\City;

use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class CoordinateType extends AbstractType
{
    

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('addressLine1', null, ['label' => 'Entities.Coordinate.fields.addressLine1', 'required' => false])
            ->add('addressLine2', null, ['label' => 'Entities.Coordinate.fields.addressLine2', 'required' => false])
            ->add('addressLine3', null, ['label' => 'Entities.Coordinate.fields.addressLine3', 'required' => false])    

//            ->add('phone', null, array('label' => 'Entities.Coordinate.fields.phone'))
            ->add('city', Select2EntityType::class, [
              'multiple' => false,
              'required' => false,
              'label' => 'Entities.Coordinate.fields.city',
              'remote_route' => 'autocomplete_city',
              'class' => City::class,
              'primary_key' => 'id',
              'text_property' => 'name',
              'minimum_input_length' => 2,
              'page_limit' => 6,
              'scroll' => true,
              'allow_clear' => true,
              'delay' => 250,
              'cache' => true,
              'cache_timeout' => 60000, // if 'cache' is true
              'language' => 'fr',
              'placeholder' => 'Entities.Coordinate.actions.select_one_city',
                    //'attr' => array('class' => 'city-select2'),
                    // 'object_manager' => $objectManager, // inject a custom object / entity manager
            ])
            
            ->add('phone', TelType::class, [
                'label' => 'Entities.Coordinate.fields.phone',
                'required' => false,
                //'help' => 'Format 000000000000',
                'attr' => ['class' => '', 'trim' => true],
            ])
            ->add('mobilePhone', TelType::class, ['label' => 'Entities.Coordinate.fields.mobilePhone', 'required' => false, 'trim' => true, 'attr' => ['class' => '', 'trim' => true]])
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => Coordinate::class,
        
        ]);
    }
    
    public function getDefaultOptions(array $options)
    {
        return [];
    }
}
