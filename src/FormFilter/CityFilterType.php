<?php

namespace App\FormFilter;

use App\Entity\Territory;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CityFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', null, ['label' => 'Entities.City.fields.name', 'required' => false])
                ->add('zipCode', null, ['label' => 'Entities.City.fields.zipCode', 'required' => false])
                ->add('name', null, ['label' => 'Entities.City.fields.name', 'required' => false])
                ->add('department', null, ['label' => 'Entities.City.fields.department', 'required' => false])
                ->add('territory', EntityType::class, [
                    'class' => Territory::class,
                    'query_builder' => fn (EntityRepository $er) => $er->createQueryBuilder('t')
                    ->orderBy('t.root, t.lft', 'ASC'),
                    'label' => 'Territoire',
                    'required' => false,
                    'attr' => ['style' => 'width:100%']])
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
        return 'city_filter';
    }
}
