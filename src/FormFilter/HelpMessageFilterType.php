<?php

namespace App\FormFilter;

use App\Entity\HelpMessage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HelpMessageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('name', null, ['label' => 'Generics.fields.name', 'required' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => HelpMessage::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'helpMessage_filter';
    }
}
