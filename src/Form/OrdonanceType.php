<?php

namespace App\Form;

use App\Entity\Medicament;
use App\Entity\Ordonance;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;



class OrdonanceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('notice')
            ->add('date', null, [
                'widget' => 'single_text',
            ])
            ->add('doctorId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('patientId', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])
            ->add('medicaments', EntityType::class, [
                'class' => Medicament::class,
                'choice_label' => 'name', // Adjust this based on the Medicament entity field
                'multiple' => true, // Allow multiple selections
                'expanded' => true, // Use a dropdown (set to true for checkboxes)
                'by_reference' => false,
                'attr' => ['class' => 'box']
            ])
            ->add('save',SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ordonance::class,
        ]);
    }
}
