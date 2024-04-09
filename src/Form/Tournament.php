<?php

namespace App\Form;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Tournament extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['row_attr' => ['class' => 'form-control'], 'label' => 'Название'])
            ->add('save', SubmitType::class, ['row_attr' => ['class' => 'form-control'], 'label' => 'Добавить',])
            ->add('team', EntityType::class, [
                'choice_attr' => function () {
                    return ['selected' => 'selected'];
                },
                'class'       => \App\Entity\Team::class,
                'multiple'    => true,
                'label'       => 'Команды',
            ]);
    }
}
