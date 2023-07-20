<?php

namespace App\FormFilter;

use App\Entity\Dysfonctionnement;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Repository\ListingValueRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class DysfonctionnementFilterType extends AbstractType
{
    use ListingValuesFormsTrait;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
              ->add('abris', null, ['label' => 'Entities.Dysfonctionnement.fields.abris', 'required' => false])
              ->add('natureDys', EntityType::class, ['label' => 'Entities.Dysfonctionnement.fields.natureDys', 'class' => ListingValue::class, 'expanded' => false, 'multiple' => false, 'required' => false, 'placeholder' => '', 'choice_attr' => function ($choiceValue, $key, $value) {
                  if ($choiceValue->getHelpMessage()) {
                      $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                      return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                  } else {
                      return [];
                  }
              }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Dysfonctionnement', 'natureDys'))])
                ->add('statusDys', EntityType::class, ['label' => 'Entities.Dysfonctionnement.fields.statusDys', 'class' => ListingValue::class, 'expanded' => false, 'required' => false, 'multiple' => false, 'placeholder' => '', 'choice_attr' => function ($choiceValue, $key, $value) {
                    if ($choiceValue->getHelpMessage()) {
                        $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                        return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                    } else {
                        return [];
                    }
                }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Dysfonctionnement', 'statusDys'))])
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
        return 'dysfonctionnement_filter';
    }
}
