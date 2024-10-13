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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CreateEnvType extends AbstractType
{
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('database_type', ChoiceType::class, [
            'label' =>  $this->translator->trans('Database type'),
            'choices'  => [
                'MySQL/MariaDB' => 'mysql',
                'PostgreSQL' => 'postgresql',
                'SQLite' => 'sqlite',
            ],
        ])
        ->add('database_host', TextType::class, [
            'label' =>  $this->translator->trans('Database hostname'),
        ])
        ->add('database_port', NumberType::class, [
            'label' =>  $this->translator->trans('Database port'),
        ])
        ->add('database_user', TextType::class, [
            'label' =>  $this->translator->trans('Database username'),
        ])
        ->add('database_password', PasswordType::class, [
            'label' =>  $this->translator->trans('Database password'),
        ])
        ->add('database_db', TextType::class, [
            'label' =>  $this->translator->trans('Database name'),
        ])

        ->add('email_host', TextType::class, [
            'label' =>  $this->translator->trans('E-mail hostname'),
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ])
        ->add('email_smtp_port', NumberType::class, [
            'label' =>  $this->translator->trans('E-mail smtp port'),
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ])
        ->add('email_imap_ssl_port', NumberType::class, [
            'label' =>  $this->translator->trans('E-mail imap ssl port'),
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ])
        ->add('email_user', TextType::class, [
            'label' =>  $this->translator->trans('E-mail username'),
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ])
        ->add('email_password', PasswordType::class, [
            'label' =>  $this->translator->trans('E-mail password'),
            'constraints' => [
                new Assert\NotBlank(),
            ]
        ])
        ->add('pushover_api_key', TextType::class, [
            'label' =>  $this->translator->trans('Pushover API key'),
            'required' => false,
        ])
        ->add('pushover_user_key', TextType::class, [
            'label' =>  $this->translator->trans('Pushover user key'),
            'required' => false,
        ])
        ->add('delete_processed_mails', ChoiceType::class, [
            'label' =>  $this->translator->trans('For each mail that has been processed'),
            'choices'  => [
                $this->translator->trans('Mark as read, and ignore') => "false",
                $this->translator->trans('Delete') => "true",
            ],
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
