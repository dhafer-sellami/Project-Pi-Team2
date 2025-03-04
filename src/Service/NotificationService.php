<?php

namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class NotificationService
{
    private $mailer;
    private $entityManager;
    private $emailFrom;

    public function __construct(
        MailerInterface $mailer,
        EntityManagerInterface $entityManager,
        string $emailFrom = 'noreply@meditrack.fr'
    ) {
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
        $this->emailFrom = $emailFrom;
    }

    public function envoyerNotification(User $utilisateur, string $titre, string $message, string $type = 'info'): void
    {
        // Créer la notification
        $notification = new Notification();
        $notification->setUtilisateur($utilisateur)
            ->setTitre($titre)
            ->setMessage($message)
            ->setType($type);

        // Sauvegarder la notification
        $this->entityManager->persist($notification);
        $this->entityManager->flush();

        // Envoyer l'email
        $this->envoyerEmail($utilisateur->getEmail(), $titre, $message);
    }

    public function envoyerRappelMedicament(User $utilisateur, string $nomMedicament, \DateTime $heurePrise): void
    {
        $titre = "Rappel de prise de médicament";
        $message = sprintf(
            "N'oubliez pas de prendre votre médicament %s à %s",
            $nomMedicament,
            $heurePrise->format('H:i')
        );

        $this->envoyerNotification($utilisateur, $titre, $message, 'rappel_medicament');
    }

    private function envoyerEmail(string $destinataire, string $titre, string $message): void
    {
        $email = (new Email())
            ->from($this->emailFrom)
            ->to($destinataire)
            ->subject($titre)
            ->text($message)
            ->html(sprintf('<h1>%s</h1><p>%s</p>', $titre, $message));

        $this->mailer->send($email);
    }

    public function marquerCommeLue(Notification $notification): void
    {
        $notification->setEstLue(true);
        $this->entityManager->flush();
    }

    public function getNotificationsNonLues(User $utilisateur): array
    {
        return $this->entityManager->getRepository(Notification::class)
            ->findBy([
                'utilisateur' => $utilisateur,
                'estLue' => false
            ], ['dateCreation' => 'DESC']);
    }
}
