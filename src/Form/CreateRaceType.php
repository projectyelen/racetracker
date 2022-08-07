<?php

namespace App\Form;

use App\Entity\Race;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CreateRaceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('raceName', TextType::class, [
                'required' => false,
                'empty_data' => ''
            ])
            ->add('date', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'empty_data' => ''
            ])
            ->add('csvFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'empty_data' => ''
            ])
            ->add('Add', SubmitType::class)
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Race::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id' => 'race_item',
        ]);
    }
}
