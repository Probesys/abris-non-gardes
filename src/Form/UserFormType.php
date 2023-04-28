<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\ListingValueRepository;
use App\Entity\ListingValue;
use App\Entity\Abris;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class UserFormType extends AbstractType
{
    
    use ListingValuesFormsTrait;
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $userType = $options['userType'];
        $isNew = $options['isNew'];
        $builder
            //->add('id')
            ->add('structureName', null, [
                'label' => 'Entities.User.fields.structureName',
              ])    
            ->add('lastName', null, [
                'label' => 'Entities.User.fields.lastName',
              ])    
            ->add('firstName', null, [
                'label' => 'Entities.User.fields.firstName',
              ])
            ->add('email', null, [
                'label' => 'Entities.User.fields.email',
              ])     
            ->add('login', HiddenType::class, [
                'label' => 'Entities.User.fields.login',
              ])
            ->add('coordinate', CoordinateType::class, [
                'label' => 'Entities.Coordinate.title',
              ])
             
            ;    
            
        if($isNew) {
            $builder->add('plainPassword', PasswordType::class, [
                'label' => 'Entities.User.fields.password',
                'required' => true,
            ]);
        }
        if ('user' === $userType) {
            $builder->add('userType', EntityType::class, [
                    'label' => 'Entities.User.fields.userType',
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
                    'query_builder' => fn(ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('User', 'userType')),
                ])
                ->add('abrisFavoris', Select2EntityType::class, [
                    'multiple' => true,
                    'required' => false,
                    'label' => 'Entities.User.fields.abrisFavoris',
                    'remote_route' => 'autocomplete_abris',
                    'class' => Abris::class,
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
                    'placeholder' => 'Entities.User.actions.select_one_or_many_abris',

                ])            
                ;
        }
        $builder->add('picture', FileType::class, [
                    'required' => false,
                    'multiple' => false,
                    'mapped' => false,
                    'label' => 'Entities.User.fields.photo',
                    'attr' => [
                        'accept' => 'image/*',
                        //'multiple' => 'false'
                    ]
                ])    
            ; 

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
        $resolver->setRequired('userType');
        $resolver->setRequired('isNew');
        
    }
}
