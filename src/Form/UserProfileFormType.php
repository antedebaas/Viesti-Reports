<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Users;

use App\Repository\UsersRepository;

class UserProfileFormType extends AbstractType
{
    private UsersRepository $UsersRepository;

    public function __construct(UsersRepository $usersRepository)
    {
        $this->UsersRepository = $usersRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('first_name')
        ->add('last_name')
        ->add('email')
        ->add('password0', PasswordType::class, [
            'label' => 'Current password',
            'mapped' => false,
            'required' => false,
        ])
        ->add('password1', PasswordType::class, [
            'label' => 'New password',
            'mapped' => false,
            'required' => false,
            'constraints' => [
                new Length([
                    'min' => 8,
                    'minMessage' => 'Your password should be at least {{ limit }} characters',
                    // max length allowed by Symfony for security reasons
                    'max' => 4096,
                ]),
                new PasswordStrength(),
                new NotCompromisedPassword(),
            ],
        ])
        ->add('password2', PasswordType::class, [
            'label' => 'Repeat new password',
            'mapped' => false,
            'required' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
