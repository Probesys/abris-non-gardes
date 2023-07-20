<?php

namespace App\Form;

use App\Entity\listingType;
use App\Entity\ListingValue;
use App\Repository\ListingTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use App\Entity\HelpMessage;

class ListingValueFormType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];

        $builder
                ->add('name', null, [
                    'label' => 'Generics.fields.name',
                    'required' => true,
                ])
                ->add('listingType', EntityType::class, [
                    'class' => listingType::class,
                    'required' => true,
                    'label' => 'Entities.ListingValue.fields.listingType',
                    //'attr' => array('class' => 'select2'),
                    'placeholder' => 'Entities.ListingValue.actions.selectlistingType',
                    'query_builder' => fn (ListingTypeRepository $rep) => $rep->createQueryBuilder('lt')
                            ->orderBy('lt.slug', 'ASC'),
                ])
                ->add('orderInList', null, [
                    'label' => 'Entities.ListingValue.fields.weight',
                    'required' => false,
                ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, [$this, 'onPreSetData']);
    }

    public function onPreSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        $ListValueRepository = $this->em->getRepository(ListingValue::class);
        $typeListe = $data->getListingType() ?: null;
        $curLevel = is_countable($data->getPath()) ? count($data->getPath()) : 0;
        if ($typeListe) {
            $id_type = $typeListe->getId();
            $typeListBuilder = $ListValueRepository->createQueryBuilder('lv')
                    ->orderBy('lv.name, lv.slug', 'ASC')
                    ->where('lv.listingType=' . $id_type);
            if ($data->getId()) {
                $typeListBuilder->andWhere('lv.id!=' . $data->getId());
            }

            $form->add('parent', EntityType::class, ['class' => ListingValue::class, 'label' => 'Entities.ListingValue.fields.listingParent', 'placeholder' => 'Entities.ListingValue.actions.selectlistingType', 'choice_label' => function ($choice, $key, $value) use ($ListValueRepository, $curLevel) {
                if ($choice->getParent()) {
                    //                        $path = $choice->getPath();
                    return implode(' » ', $choice->getPath()) . ' » ' . $choice->getName();
                } else {
                    return $choice;
                }
            }, 'choice_attr' => function ($choiceValue, $key, $value) use ($ListValueRepository, $curLevel, $data) {
                $path = $choiceValue->getPath();
                if ($data->getParent() && (is_countable($path) ? count($path) : 0) >= $curLevel) {
                    return ['class' => 'd-none', 'disabled' => true];
                } else {
                    return [];
                }
            }, 'query_builder' => $typeListBuilder, 'required' => false]);
        }

        $form->add('helpMessage', Select2EntityType::class, [
            'multiple' => false,
            'label' => 'Entities.ListingValue.fields.helpMessage',
            'remote_route' => 'autocomplete_search_help_message',
            'class' => HelpMessage::class,
            'primary_key' => 'id',
            'text_property' => 'name',
            'minimum_input_length' => 2,
            'page_limit' => 10,
            'scroll' => true,
            'allow_clear' => true,
            'delay' => 250,
            'cache' => true,
            'cache_timeout' => 60000, // if 'cache' is true
            'language' => 'fr',
            'placeholder' => 'Entities.HelpMessage.actions.selectOneHelpMessage',
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ListingValue::class,
        ]);
        $resolver->setRequired('translator');
    }
}
