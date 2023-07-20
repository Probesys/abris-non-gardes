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
