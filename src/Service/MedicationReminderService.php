<?php

namespace App\Service;

use App\Entity\PriseMedicament;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Twig\Environment;

class MedicationReminderService
{
    private $entityManager;
    private $mailer;
    private $emailFrom;
    private $twig;

    public function __construct(
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        string $emailFrom,
        Environment $twig
    ) {
        $this->entityManager = $entityManager;
        $this->mailer = $mailer;
        $this->emailFrom = $emailFrom;
        $this->twig = $twig;
    }

    public function createMedicationReminder(PriseMedicament $priseMedicament): void
    {
        $notification = new Notification();
        $medicament = $priseMedicament->getMedicament();
        
        $titre = sprintf('Rappel : Prenez votre médicament %s', $medicament->getNom());
        
        $message = sprintf(
            "Il est temps de prendre votre médicament :\n\n" .
            "Médicament : %s\n" .
            "Dosage : %s\n" .
            "Fréquence : %s\n" .
            "Heure prévue : %s",
            $medicament->getNom(),
            $medicament->getDosage() ?? 'Non spécifié',
            $medicament->getFrequence() ?? 'Non spécifiée',
            $priseMedicament->getDateHeurePrise()->format('H:i')
        );

        $notification->setTitre($titre);
        $notification->setMessage($message);
        $notification->setType('medication_reminder');
        $notification->setUtilisateur($priseMedicament->getPatient());
        $notification->setEstLue(false);

        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        // Envoyer l'email
        $this->sendEmailNotification($notification);
    }

    public function checkAndSendReminders(): void
    {
        $now = new \DateTime();
        $interval = new \DateInterval('PT15M'); // 15 minutes
        $endTime = (clone $now)->add($interval);

        $priseMedicaments = $this->entityManager
            ->getRepository(PriseMedicament::class)
            ->findUpcomingMedications($now, $endTime);

        foreach ($priseMedicaments as $priseMedicament) {
            $this->createMedicationReminder($priseMedicament);
        }
    }

    private function sendEmailNotification(Notification $notification): void
    {
        $emailContent = $this->twig->render('emails/medication_reminder.html.twig', [
            'notification' => $notification
        ]);

        $email = (new Email())
            ->from($this->emailFrom)
            ->to($notification->getUtilisateur()->getEmail())
            ->subject($notification->getTitre())
            ->html($emailContent);

        $this->mailer->send($email);
    }
}
