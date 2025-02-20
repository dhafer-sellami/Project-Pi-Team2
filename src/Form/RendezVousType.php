<?php

namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class RendezVousType extends AbstractType
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('date', DateTimeType::class, [
                'widget' => 'single_text',
                'constraints' => [
                    new Callback([$this, 'validateDateLimit']),
                ],
            ])
            
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'box',
                    'placeholder' => 'Saisissez votre email'
                ]
            ])

            ->add('num', IntegerType::class, [
                'attr' => [
                    'class' => 'box',
                    'placeholder' => 'Saisissez votre numéro'
                ]
            ])

            ->add('etat', stringType::class, [
                'attr' => [
                    'class' => 'box',
                    'placeholder' => 'Saisissez votre etat'
                ]
            ])
        
            
            ->add('num')


            ->add('etat')
            
            
            ->add('save', SubmitType::class);
    }

    public function validateDateLimit($date, ExecutionContextInterface $context)
{
    if ($date) {
        $startOfDay = (clone $date)->setTime(00, 00, 00);
        $endOfDay = (clone $date)->setTime(23, 59, 59);

        // Compte les rendez-vous existants pour cette date
        $count = $this->entityManager->getRepository(RendezVous::class)
            ->createQueryBuilder('r')
            ->select('COUNT(r.id)')
            ->where('r.date BETWEEN :start AND :end')
            ->setParameter('start', $startOfDay)
            ->setParameter('end', $endOfDay)
            ->getQuery()
            ->getSingleScalarResult();

        if ($count >= 2) {
            $context->buildViolation("Il y a déjà 10 rendez-vous pour cette date. Veuillez en choisir une autre.")
                ->atPath('date')
                ->addViolation();
        }
    }
}



    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}