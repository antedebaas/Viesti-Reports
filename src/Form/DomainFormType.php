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

use App\Repository\MXRecordsRepository;
use App\Entity\MXRecords;

class DomainFormType extends AbstractType
{
    private MXRecordsRepository $MXRecordsRepository;

    public function __construct(MXRecordsRepository $MXRecordsRepository,)
    {
        $this->MXRecordsRepository = $MXRecordsRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fqdn', TextType::class, [
                'label' => 'Domain name',
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
                'attr' => array(
                    'min' => '86400',
                    'max' => '31557600',
                ),
            ])
            ->add('mx_records', CollectionType::class, [
                'label' => "MX Records",
                'entry_type' => MXRecordsEmbeddedFormType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'prototype_data' => new MXRecords(),
                'entry_options' => ['label' => false],
                'by_reference' => false,
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
