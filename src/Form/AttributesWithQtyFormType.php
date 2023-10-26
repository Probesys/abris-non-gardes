<?php

namespace App\Form;

use App\Entity\AttributesWithQty;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\ListingValueRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AttributesWithQtyFormType extends AbstractType
{
    use ListingValuesFormsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $listingType = $options['listingType'];
        $builder
//        ->add('abris', HiddenType::class, [
//                    'label' => false
//                ])
                ->add('listingValue', EntityType::class, ['label' => false, 'class' => ListingValue::class, 'expanded' => false, 'multiple' => false, 'placeholder' => 'Generics.labels.type', 'required' => true, 'choice_attr' => function ($choiceValue, $key, $value) {
                    if ($choiceValue->getHelpMessage()) {
                        $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                        return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                    } else {
                        return [];
                    }
                }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $listingType)])
                ->add('qty', null, [
                    'label' => false,
                    'attr' => ['pattern' => '^\d+$'],
//                    'placeholder' => 'Generics.fields.qty',
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AttributesWithQty::class,
        ]);
        $resolver->setRequired('listingType');
    }
}
