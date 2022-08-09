<?php

namespace App\Form;

use App\Entity\Result;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class EditResultType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, array(
                'required' => false,
                'empty_data' => ''
            ))
            ->add('raceTime', TextType::class,  [
                'required' => false,
                'empty_data' => ''
            ])
            ->add('Edit', SubmitType::class)
        ;

        //Form formating time to seconds and vice versa
        $builder
            ->get('raceTime')
            ->addModelTransformer(new CallbackTransformer(
                function ($timeAsSeconds) {
                    $hours = floor($timeAsSeconds / 3600);
                    $mins = floor($timeAsSeconds / 60 % 60);
                    $secs = floor($timeAsSeconds % 60);

                    $time = sprintf('%02d', $hours) . ':' . sprintf('%02d', $mins) . ':' . sprintf('%02d', $secs);

                    return $time;
                },
                function ($timeAsTime) {
                    $parsed = date_parse($timeAsTime);
                    $seconds = $parsed['hour'] * 3600 + $parsed['minute'] * 60 + $parsed['second'];
                    return $seconds;
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Result::class
        ]);
    }
}

