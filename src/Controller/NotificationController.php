<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Notification;
use App\Entity\PriseMedicament;
use App\Service\MedicationReminderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\HttpFoundation\JsonResponse;
use Twig\Environment;

#[Route('/notification')]
#[IsGranted('ROLE_USER')]
class NotificationController extends AbstractController
{
    private $mailer;
    private $medicationReminderService;
    private $twig;

    public function __construct(
        MailerInterface $mailer, 
        MedicationReminderService $medicationReminderService,
        Environment $twig
    ) {
        $this->mailer = $mailer;
        $this->medicationReminderService = $medicationReminderService;
        $this->twig = $twig;
    }

    #[Route('/', name: 'app_notification_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $notifications = $entityManager
            ->getRepository(Notification::class)
            ->findBy(['utilisateur' => $this->getUser()], ['dateCreation' => 'DESC']);

        return $this->render('notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/test-reminder', name: 'app_notification_test_reminder', methods: ['GET'])]
    public function testReminder(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        try {
            // Récupérer la prochaine prise de médicament
            $priseMedicament = $entityManager
                ->getRepository(PriseMedicament::class)
                ->findOneBy(['patient' => $user, 'pris' => false], ['dateHeurePrise' => 'ASC']);

            if (!$priseMedicament) {
                $this->addFlash('warning', 'Aucune prise de médicament prévue n\'a été trouvée.');
                return $this->redirectToRoute('app_notification_index');
            }

            // Créer et envoyer la notification
            $this->medicationReminderService->createMedicationReminder($priseMedicament);

            $this->addFlash('success', 'Notification de test envoyée ! Vérifiez votre email.');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de la notification : ' . $e->getMessage());
        }

        return $this->redirectToRoute('app_notification_index');
    }

    #[Route('/generate-report', name: 'app_notification_generate_report', methods: ['GET'])]
    public function generateReport(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        try {
            $startDate = new \DateTime('first day of this month');
            $endDate = new \DateTime('last day of this month');

            // Récupérer les notifications de l'utilisateur
            $notifications = $entityManager
                ->getRepository(Notification::class)
                ->findByUserAndDateRange($user, $startDate, $endDate);

            // Générer le PDF
            $html = $this->twig->render('reports/notification_report.html.twig', [
                'notifications' => $notifications,
                'user' => $user,
                'startDate' => $startDate,
                'endDate' => $endDate
            ]);

            $options = new Options();
            $options->set('isHtml5ParserEnabled', true);
            $options->set('isPhpEnabled', true);

            $dompdf = new Dompdf($options);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();

            $response = new Response($dompdf->output());
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'attachment;filename="rapport_notifications.pdf"');

            return $response;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la génération du rapport : ' . $e->getMessage());
            return $this->redirectToRoute('app_notification_index');
        }
    }

    #[Route('/{id}/mark-as-read', name: 'app_notification_mark_read', methods: ['POST'])]
    public function markAsRead(
        Notification $notification,
        EntityManagerInterface $entityManager
    ): Response {
        if ($notification->getUtilisateur() !== $this->getUser()) {
            throw $this->createAccessDeniedException();
        }

        $notification->setEstLue(true);
        $entityManager->flush();

        $this->addFlash('success', 'Notification marquée comme lue.');
        return $this->redirectToRoute('app_notification_index');
    }

    #[Route('/non-lues', name: 'non_lues', methods: ['GET'])]
    public function getNotificationsNonLues(EntityManagerInterface $entityManager): JsonResponse
    {
        $utilisateur = $this->getUser();
        $notifications = $entityManager
            ->getRepository(Notification::class)
            ->findBy(['utilisateur' => $utilisateur, 'estLue' => false]);

        return new JsonResponse([
            'count' => count($notifications),
            'notifications' => array_map(function($notification) {
                return [
                    'id' => $notification->getId(),
                    'titre' => $notification->getTitre(),
                    'message' => $notification->getMessage(),
                    'type' => $notification->getType(),
                    'dateCreation' => $notification->getDateCreation()->format('Y-m-d H:i:s')
                ];
            }, $notifications)
        ]);
    }

    #[Route('/marquer-comme-lue/{id}', name: 'marquer_lue', methods: ['POST'])]
    public function marquerCommeLue(int $id, EntityManagerInterface $entityManager): JsonResponse
    {
        $notification = $entityManager
            ->getRepository(Notification::class)
            ->find($id);
        
        if (!$notification) {
            return new JsonResponse(['success' => false], Response::HTTP_NOT_FOUND);
        }

        $notification->setEstLue(true);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
