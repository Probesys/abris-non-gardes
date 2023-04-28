<?php

namespace App\Form;

use App\Entity\Abris;
use App\Entity\City;
use App\Entity\ListingValue;
use App\Entity\User;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\ListingValueRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class AbrisFormType extends AbstractType
{
    private $em;

    use ListingValuesFormsTrait;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //$translator = $options['translator'];
        $builder
                ->add('type', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.type',
                    'class' => ListingValue::class,
                    'required' => true,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => '',
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'type'));
                    },
                ))

                ->add('name', null, [
                    'label' => 'Entities.Abris.fields.name',
                ])
                ->add('coordinate', null, [
                    'label' => 'Entities.Abris.fields.coordinate',
                    'help' => 'format : "lat,lng", ex: "45.179225,5.724737"<br/> <a href="https://www.latlong.net/convert-address-to-lat-long.html" target="_blank">www.latlong.net</a>',

                ])
                ->add('altitude', null, [
                    'label' => 'Entities.Abris.fields.altitude',
                ])
                ->add('city', Select2EntityType::class, [
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Entities.Abris.fields.city',
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
                    'placeholder' => 'Entities.Abris.actions.select_one_city',
                        //'attr' => array('class' => 'city-select2'),
                        // 'object_manager' => $objectManager, // inject a custom object / entity manager
                ])
                ->add('proprietaires', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.proprietaires',
                    'class' => User::class,
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false,
                    'placeholder' => '',
                    'query_builder' => function (UserRepository  $rep) {
                        return $rep->createQueryBuilder('u')
                                ->where('u.roles LIKE  \'%ROLE_OWNER%\'');
                    },
                ))
                ->add('gestionnaires', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.gestionnaires',
                    'class' => User::class,
                    'expanded' => false,
                    'multiple' => true,
                    'required' => false,
                    'placeholder' => '',
                    'query_builder' => function (UserRepository $rep) {
                        return $rep->createQueryBuilder('u')
                                ->where('u.roles LIKE \'%ROLE_MANAGER%\'');
                    },
                ))
                ->add('capaciteAccueil', null, [
                    'label' => 'Entities.Abris.fields.capaciteAccueil',
                    'attr' => [
                      'min' => 0,
                    ]
                ])
                ->add('capaciteCouchage', null, [
                    'label' => 'Entities.Abris.fields.capaciteCouchage',
                    'attr' => [
                      'min' => 0,
                    ]
                ])
                ->add('description', null, [
                    'label' => 'Entities.Abris.fields.description',
                ])
                ->add('toit', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.toit',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => true,
                    'placeholder' => '',
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'toit'));
                    },
                ))
                ->add('sortieFumees', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.sortieFumees',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'sortieFumees'));
                    },
                ))
                ->add('materiauSortieFumees', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.materiauSortieFumees',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'materiauSortieFumees'));
                    },
                ))
                ->add('nbPortes', null, [
                    'label' => 'Entities.Abris.fields.nbPortes',
                ])
                ->add('nbFenetres', null, [
                    'label' => 'Entities.Abris.fields.nbFenetres',
                ])
                ->add('typeMur', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.typeMur',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => '',
                    'required' => true,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'typeMur'));
                    },
                ))
                ->add('etage', null, [
                    'label' => 'Entities.Abris.fields.etage',
                    'attr' => [
                      'class' => 'hiddenPilotField',
                      'data-hiddedClass' => 'etageDetails',
                    ],
                ])
                ->add('accesEtage', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.accesEtage',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => true,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'accesEtage'));
                    },
                    'attr' => [
                        'class' => 'etageDetails',
                    ],
                ))
                ->add('typeAccesEtage', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.typeAccesEtage',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => true,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'typeAccesEtage'));
                    },
                    'attr' => [
                        'class' => 'etageDetails',
                    ],
                ))
                ->add('typeSol', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.typeSol',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => true,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'typeSol'));
                    },
                ))
                ->add('citerneExterieure', null, [
                    'label' => 'Entities.Abris.fields.citerneExterieure',
                ])
                ->add('appentisExterieur', null, [
                    'label' => 'Entities.Abris.fields.appentisExterieur',
                ])
                ->add('nbAncrageSol', null, [
                    'label' => 'Entities.Abris.fields.nbAncrageSol',
                ])
                ->add('typeAncrageSol', EntityType::class, array(
                    'label' => 'Entities.Abris.fields.typeAncrageSol',
                    'class' => ListingValue::class,
                    'expanded' => false,
                    'multiple' => false,
                    'placeholder' => '',
                    'required' => false,
                    'choice_attr' => function ($choiceValue, $key, $value) {
                        if ($choiceValue->getHelpMessage()) {
                            $id_helpMessage = $choiceValue->getHelpMessage()->getId();

                            return ['class' => 'with-help-message', 'data-id-help-message' => $id_helpMessage];
                        } else {
                            return [];
                        }
                    },
                    'query_builder' => function (ListingValueRepository $rep) {
                        return $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('Abris', 'typeAncrageSol'));
                    },
                ))
                ->add('remarqueStructureBat', null, [
                    'label' => 'Entities.Abris.fields.remarqueStructureBat',
                ])
                ->add('chemineeEnPierreSurLeToit')
                ->add('mobiliers', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AttributesWithQtyFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'listingType' => $this->getUuidTypeListeFromAnnotation('Abris', 'mobiliers'),
                    ],
                    'by_reference' => false,
                ])
                ->add('couchages', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AttributesWithQtyFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'listingType' => $this->getUuidTypeListeFromAnnotation('Abris', 'couchages'),
                    ],
                    'by_reference' => false,
                ])
                ->add('placeDeFeuInterieur', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AttributesWithQtyFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'listingType' => $this->getUuidTypeListeFromAnnotation('Abris', 'placeDeFeuInterieur'),
                    ],
                    'by_reference' => false,
                ])
                ->add('toilettesSeches', null, [
                    'label' => 'Entities.Abris.fields.toilettesSeches'
                ])
                ->add('mobilierPiqueniqueExterieur', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AttributesWithQtyFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'listingType' => $this->getUuidTypeListeFromAnnotation('Abris', 'mobilierPiqueniqueExterieur'),
                    ],
                    'by_reference' => false,
                ])
                ->add('placeDeFeuExterieure', null, [
                    'label' => 'Entities.Abris.fields.placeDeFeuExterieur'
                ])
                ->add('emplacementInterieurReserveBois', null, [
                    'label' => 'Entities.Abris.fields.emplacementInterieurReserveBois'
                ])
                ->add('materielDivers', CollectionType::class, [
                    'label' => false,
                    'entry_type' => AttributesWithQtyFormType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'entry_options' => [
                        'listingType' => $this->getUuidTypeListeFromAnnotation('Abris', 'materielDivers'),
                    ],
                    'by_reference' => false,
                ])
                ->add('source', null, [
                    'label' => 'Entities.Abris.fields.source',
                ])
                ->add('nomSource', null, [
                    'label' => 'Entities.Abris.fields.nomSource',
                ])
                ->add('coordinateSource', null, [
                    'label' => 'Entities.Abris.fields.coordinateSource',
                    'help' => 'format : "lat,lng", ex: "45.179225,5.724737"<br/> <a href="https://www.latlong.net/convert-address-to-lat-long.html" target="_blank">www.latlong.net</a>',
                ])
                ->add('eauCourante', null, [
                    'label' => 'Entities.Abris.fields.eauCourante',
                ])
                ->add('cahierSuiviEtCrayon', null, [
                    'label' => 'Entities.Abris.fields.cahierSuiviEtCrayon',
                ])
                ->add('plaqueAbris')
                ->add('panneauInfosBonnesPratiques')
                ->add('signaletiqueSourceProche')
                ->add('files', FileType::class, [
                    'required' => false,
                    'multiple' => true,
                    'mapped' => false,
                    'attr' => [
                        'accept' => 'image/*',
                        'multiple' => 'multiple'
                    ]
                ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Abris::class,
        ]);
        $resolver->setRequired('translator');
    }
}
