<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Users;
use App\Entity\Domains;

use App\Repository\DomainsRepository;
use App\Repository\UsersRepository;

class UserFormType extends AbstractType
{
    private DomainsRepository $DomainsRepository;
    private UsersRepository $UsersRepository;

    public function __construct(DomainsRepository $domainsRepository, UsersRepository $usersRepository)
    {
        $this->DomainsRepository = $domainsRepository;
        $this->UsersRepository = $usersRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email')
        ->add('password1', PasswordType::class, [
            'label' => 'New password',
            'mapped' => false,
            'required' => false,
        ])
        ->add('password2', PasswordType::class, [
            'label' => 'Repeat new password',
            'mapped' => false,
            'required' => false,
        ])

        ->add('roles', EntityType::class, [
            'class' => Domains::class,
            'multiple' => true,
            'required' => false,
            'mapped' => false,
            'data' => $this->DomainsRepository->findFormSelectedRoles($options),
            'choice_label' => function ($domain) {
                return $domain->getFqdn();
            },

            'choices' => $this->DomainsRepository->findBy(array()),
        ])
        ->add('isVerified')
        ->add('isAdmin', CheckboxType::class, [
            'label' => 'Is admin',
            'mapped' => false,
            'data' => $this->UsersRepository->findFormIsAdmin($options),
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
