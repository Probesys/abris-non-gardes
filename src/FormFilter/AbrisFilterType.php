<?php

namespace App\FormFilter;

use App\Entity\Abris;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\ListingValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AbrisFilterType extends AbstractType
{
    use ListingValuesFormsTrait;

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', null, ['label' => 'Generics.fields.name', 'required' => false])
        ->add('type', EntityType::class, ['label' => 'Entities.Abris.fields.type', 'class' => ListingValue::class, 'expanded' => false, 'multiple' => false, 'placeholder' => '', 'required' => false, 'choice_attr' => function ($choiceValue, $key, $value) {
            if ($choiceValue->getHelpMessage()) {
                $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
            } else {
                return [];
            }
        }, 'query_builder' => fn(ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'type'))]) 

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => Abris::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'abris_filter';
    }
}
