<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreateEnvType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

        ->add('database_host', TextType::Class, [
            'label' =>  $this->translator->trans('Database hostname'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => 'localhost'
        ])
        ->add('database_port', NumberType::Class, [
            'label' =>  $this->translator->trans('Database port'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => '3306'
        ])
        ->add('database_user', TextType::Class, [
            'label' =>  $this->translator->trans('Database username'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => ''
        ])
        ->add('database_password', PasswordType::Class, [
            'label' =>  $this->translator->trans('Database password'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => ''
        ])
        ->add('database_db', TextType::Class, [
            'label' =>  $this->translator->trans('Database name'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => ''
        ])

        ->add('email_host', TextType::Class, [
            'label' =>  $this->translator->trans('E-mail hostname'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => 'localhost'
        ])
        ->add('email_smtp_port', NumberType::Class, [
            'label' =>  $this->translator->trans('E-mail smtp port'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => '25'
        ])
        ->add('email_imap_ssl_port', NumberType::Class, [
            'label' =>  $this->translator->trans('E-mail imap ssl port'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => '993'
        ])
        ->add('email_user', TextType::Class, [
            'label' =>  $this->translator->trans('E-mail username'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => ''
        ])
        ->add('email_password', PasswordType::Class, [
            'label' =>  $this->translator->trans('E-mail password'),
            'constraints' => [
                new Assert\NotBlank(),
            ],
            'empty_data' => ''
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
