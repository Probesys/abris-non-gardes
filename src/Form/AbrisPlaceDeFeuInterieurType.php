<?php

namespace App\Form;

use App\Entity\AbrisPlaceDeFeuInterieur;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\ListingValueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbrisPlaceDeFeuInterieurType extends AbstractType
{
    use ListingValuesFormsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('qty', NumberType::class, [
                    'label' => 'quantité'
                ])
                ->add('typePlace', EntityType::class, ['label' => 'Entities.Abris.fields.typePlace', 'class' => ListingValue::class, 'expanded' => false, 'multiple' => false, 'placeholder' => '', 'required' => false, 'choice_attr' => function ($choiceValue, $key, $value) {
                    if ($choiceValue->getHelpMessage()) {
                        $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                        return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                    } else {
                        return [];
                    }
                }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('AbrisPlaceDeFeuInterieur', 'typePlace'))])
//                ->add('listingValue')
//                ->add('abris')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AbrisPlaceDeFeuInterieur::class,
        ]);
    }

}
