<?php

namespace App\FormFilter;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
              ->add('abris', null, ['label' => 'Entities.Dysfonctionnement.fields.abris', 'required' => false, 'mapped' => false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
        'data_class' => Message::class,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'message_filter';
    }
}
