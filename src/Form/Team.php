<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class Team extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['row_attr' => ['class' => 'form-control'], 'label' => 'Название'])
            ->add('save', SubmitType::class, ['attr' => ['class' => 'btn btn-primary'], 'row_attr' => ['class' => 'form-control'], 'label' => 'Добавить',]);
    }
}
