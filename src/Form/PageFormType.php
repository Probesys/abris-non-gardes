<?php

namespace App\Form;

use App\Entity\Page;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name', null, ['label' => 'Entities.Page.fields.name'])
              ->add('linkText', null, ['label' => 'Entities.Page.fields.linkText', 'required' => true])  
              ->add('body', null, ['label' => 'Entities.Page.fields.body', 'attr' => ['class' => 'summernote']])
              
              ->add('orderInList', null, [
                'label' => 'Entities.ListingValue.fields.weight',
                'required' => false,
              ])
                ->add('dontListedInFrontPage', null, ['label' => 'Entities.Page.fields.dontListedInFrontPage'])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => Page::class,
        ]);
    }
}
