<?php

namespace App\Form;

use App\Entity\Domains;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Validator\Constraints\File;

use App\Repository\MXRecordsRepository;
use App\Entity\MXRecords;

class DomainFormType extends AbstractType
{
    private MXRecordsRepository $MXRecordsRepository;

    public function __construct(MXRecordsRepository $MXRecordsRepository)
    {
        $this->MXRecordsRepository = $MXRecordsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fqdn', TextType::class, [
                'label' => 'Domain name',
            ])
            ->add('mailhost', TextType::class, [
                'label' => 'Mailhost',
            ])
            ->add('sts_version', ChoiceType::class, [
                'choices'  => [
                    'STSv1' => 'STSv1'
                ],
            ])
            ->add('sts_mode', ChoiceType::class, [
                'choices'  => [
                    'Testing' => 'testing',
                    'Enforce' => 'enforce',
                    'None (disable STS)' => 'none',
                ],
            ])
            ->add('sts_maxage', NumberType::class, [
                'label' => 'Max age (seconds)',
                'html5' => true,
                'scale' => 0,
                'data' => '86400',
                'attr' => array(
                    'min' => '86400',
                    'max' => '31557600',
                ),
            ])
            ->add('mx_records', CollectionType::class, [
                'label' => false,
                'entry_type' => MXRecordsEmbeddedFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'prototype_data' => new MXRecords(),
                'entry_options' => ['label' => false],
                'by_reference' => false,
            ])

            ->add('dkimselector', TextType::class, [
                'label' => 'DKIM selector name',
                'data' => 'default',
            ])

            ->add('bimiselector', TextType::class, [
                'label' => 'BIMI selector name',
                'data' => 'default',
            ])

            ->add('bimisvgfile', FileType::class, [
                'label' => 'BIMI Logo SVG file',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10m',
                        'mimeTypes' => [
                            'image/svg+xml',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid SVG image',
                    ])
                ],
            ])

            ->add('bimivmcfile', FileType::class, [
                'label' => 'BIMI Logo Certificate file',

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using attributes
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '10m',
                        'mimeTypes' => [
                            'application/x-pem-file',
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid Certificate file',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Domains::class,
        ]);
    }
}
