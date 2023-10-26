<?php

namespace App\FormFilter;

use App\Form\Traits\ListingValuesFormsTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DiscussionFilterType extends AbstractType
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
        return 'discussionfilter';
    }
}
