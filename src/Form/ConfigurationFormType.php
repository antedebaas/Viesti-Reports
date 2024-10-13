<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use App\Entity\Config;

class ConfigurationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        foreach($options['data']['entries'] as $name => $item) {
            switch ($item->getType()) {
                case 'text':
                    $builder->add($item->getName(), TextType::class, [
                        'data' => $item->getValue(),
                        'label' => $item->getName(),
                        'required' => false,
                    ]);
                    break;
                case 'boolean':
                    $builder->add($item->getName(), ChoiceType ::class, [
                        'choices' => [
                            'True' => true,
                            'False' => false,
                        ],
                        'data' => $item->getValue() == '1' ?? true :: false,
                        'label' => $item->getName(),
                        'required' => true,
                    ]);
                    break;
            }
        }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
