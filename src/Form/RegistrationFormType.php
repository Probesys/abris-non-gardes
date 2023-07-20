<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use App\Entity\ListingValue;
use App\Form\Traits\ListingValuesFormsTrait;
use App\Repository\ListingValueRepository;

class RegistrationFormType extends AbstractType
{
    use ListingValuesFormsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $translator = $options['translator'];
        $builder
            ->add('lastName', null, [
                'label' => 'Entities.User.fields.lastName',
                'attr' => [
                  'style' => 'text-transform: uppercase;',
                ]
              ])
            ->add('firstName', null, [
                'label' => 'Entities.User.fields.firstName',
              ])
            ->add('email', null, [
              'label' => 'Security.fields.email',
            ])
            ->add('login', HiddenType::class, [
                'label' => 'Entities.User.fields.login',
              ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Security.fields.agreeTerms',
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => $translator->trans('Security.messages.youShouldAgreeToOurTerms'),
                    ]),
                ],

            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Security.fields.password',
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => $translator->trans('Security.messages.pleaseEnterAPassword'),
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => $translator->trans('Security.messages.minLengthPasswordMessage'),
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
            ->add('userType', EntityType::class, [
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
                    'query_builder' => fn (ListingValueRepository $rep) => $this->createListingValueBuilder($rep, $this->getUuidTypeListeFromAnnotation('User', 'userType')),
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
        $resolver->setRequired('translator');
    }
}
