<?php

namespace App\Form;

use App\Entity\Abris;
use App\Entity\Dysfonctionnement;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\AbrisRepository;
use App\Repository\ListingValueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DysfonctionnementType extends AbstractType
{
    use ListingValuesFormsTrait;

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // abris
        $user = $options['user'];
        if (!$user || ($user && ($user->hasRole('ROLE_USER') || $user->hasRole('ROLE_ADMIN')))) {
            $builder->add('abris', null, [
                    'label' => 'Entities.Dysfonctionnement.fields.abris',
                    'required' => true,
                    'placeholder' => '',
                ]);
        } else {
            $builder->add('abris', EntityType::class, [
                    'label' => 'Entities.Dysfonctionnement.fields.abris',
                    'class' => Abris::class,
                    'required' => true,
                    'placeholder' => '',
                    'expanded' => false,
                    'multiple' => false,
                    'query_builder' => fn (AbrisRepository $er) => $er->createQueryBuilder('a')
                        ->leftJoin('a.createdBy', 'crea')
                        ->leftJoin('a.proprietaires', 'proprios')
                        ->leftJoin('a.gestionnaires', 'gests')
                        ->andWhere('crea.id=\''.$user->getId().'\' OR proprios.id=\''.$user->getId().'\' OR gests.id=\''.$user->getId().'\''),
                ]);
        }

        $builder
                ->add('statusDys', EntityType::class, ['label' => 'Entities.Dysfonctionnement.fields.statusDys', 'class' => ListingValue::class, 'expanded' => false, 'required' => true, 'multiple' => false, 'placeholder' => '', 'choice_attr' => function ($choiceValue, $key, $value) {
                    if ($choiceValue->getHelpMessage()) {
                        $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                        return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                    } else {
                        return [];
                    }
                }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Dysfonctionnement', 'statusDys'))])
                ->add('natureDys', EntityType::class, ['label' => 'Entities.Dysfonctionnement.fields.natureDys', 'class' => ListingValue::class, 'expanded' => false, 'required' => true, 'multiple' => false, 'placeholder' => '', 'choice_attr' => function ($choiceValue, $key, $value) {
                    if ($choiceValue->getHelpMessage()) {
                        $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                        return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                    } else {
                        return [];
                    }
                }, 'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Dysfonctionnement', 'natureDys'))])
                ->add('description', null, [
                    'label' => 'Generics.fields.description',
                    'attr' => ['class' => 'summernote'],
                ])

                ->add('files', FileType::class, [
                    'label' => 'Entities.Dysfonctionnement.fields.files',
                    'required' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'attr' => [
                        'accept' => 'image/*',
                        'multiple' => 'multiple',
                    ],
                ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
        $builder->addEventListener(FormEvents::PRE_SUBMIT, [$this, 'onPreSubmit']);
    }

    public function addElements(FormInterface $form, $natureDys)
    {
        $ListValueRepository = $this->em->getRepository(ListingValue::class);

        if ($natureDys) {
            $typeListBuilder = $ListValueRepository->createQueryBuilder('lv')
                    ->orderBy('lv.name, lv.slug', 'ASC')
                    ->where('lv.parent='.$natureDys);

            $form->add('elementDys', EntityType::class, ['class' => ListingValue::class, 'label' => 'Entities.Dysfonctionnement.fields.elementDys', 'choice_attr' => function ($choiceValue, $key, $value) {
                if ($choiceValue->getHelpMessage()) {
                    $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                    return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                } else {
                    return [];
                }
            }, 'query_builder' => $typeListBuilder]);
        }
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $natureDys = $data->getNatureDys() ? $data->getNatureDys()->getId() : null;
        $elementDys = $data->getElementDys() ? $data->getElementDys()->getId() : null;

        $this->addElements($form, $natureDys);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $data = $event->getData();

        $natureDys = array_key_exists('natureDys', $data) && !empty($data['natureDys']) ? $data['natureDys'] : null;
        $elementDys = array_key_exists('elementDys', $data) && !empty($data['elementDys']) ? $data['elementDys'] : null;

        $this->addElements($form, $natureDys);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Dysfonctionnement::class,
            'user' => null,
        ]);
    }
}
