<?php

namespace App\Controller\Admin;

use App\Entity\RendezVous;
use App\Form\RendezVousType;
use App\Repository\RendezVousRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\Facture;
use App\Form\Search2RendezVousType;
use Knp\Component\Pager\PaginatorInterface; 
use App\Service\RendezVousHoraireService;


#[Route('/admin/rendez/vous')]
final class AdminRendezVousController extends AbstractController
{
    #[Route(name: 'app_admin_rendez_vous_index', methods: ['GET'])]
public function index(Request $request, RendezVousRepository $rendezVousRepository, PaginatorInterface $paginator): Response
{
    $form = $this->createForm(Search2RendezVousType::class);
    $form->handleRequest($request);

    $queryBuilder = $rendezVousRepository->createQueryBuilder('r')
        ->orderBy('r.date', 'DESC');

    if ($form->isSubmitted() && $form->isValid()) {
        $criteria = $form->getData();

        if (!empty($criteria['etat'])) {
            $queryBuilder->andWhere('r.etat LIKE :etat')
                         ->setParameter('etat', '%' . $criteria['etat'] . '%');
        }
        
    }

    $pagination = $paginator->paginate(
        $queryBuilder->getQuery(),
        $request->query->getInt('page', 1),
        5
    );

    return $this->render('admin/admin_rendez_vous/index.html.twig', [
        'rendez_vouses' => $pagination,
        'searchForm' => $form->createView(),
    ]);
}

    

    #[Route('/new', name: 'app_admin_rendez_vous_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,RendezVousHoraireService $horaireService): Response
    {
        $rendezVou = new RendezVous();
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $date = $form->get('date')->getData();

            if (!$horaireService->estHoraireValide($date)) {
                $this->addFlash('danger', 'Les rendez-vous doivent être entre 08h00 et 17h00 et aux minutes 00 ou 30.');
                return $this->redirectToRoute('app_admin_rendez_vous_new');
            }
    

            

            
            $rendezVou->setDate($date); 
            $entityManager->persist($rendezVou);
            $entityManager->flush();

            return $this->render('rendez_vous/index2.html.twig');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rendez_vous_show', methods: ['GET'])]
    public function show(RendezVous $rendezVou): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez_vou' => $rendezVou,
        ]);
    }

    #[Route('{id}/edit', name: 'app_admin_rendez_vous_edit')]
    public function edit(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezVousType::class, $rendezVou);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rendez_vous/edit.html.twig', [
            'rendez_vou' => $rendezVou,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_rendez_vous_delete', methods: ['POST'])]
    public function delete(Request $request, RendezVous $rendezVou, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rendezVou->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rendezVou);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_rendez_vous_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/confirm/{id}', name: 'app_admin_rendez_vous_confirm')]
    public function confirm(int $id, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $rendezVous = $entityManager->getRepository(RendezVous::class)->find($id);

        if (!$rendezVous) {
            throw $this->createNotFoundException('Rendez-vous non trouvé.');
        }

       
        $facture = $entityManager->getRepository(Facture::class)->findOneBy(['idrdv' => $rendezVous]);

        if (!$facture) {
            throw new \Exception('Aucune facture associée à ce rendez-vous.');
        }

        
        $emailPatient = $rendezVous->getEmail(); 

       
        $email = (new Email())
            ->from('ESP8266ARDPROJ@gmail.com')
            ->to($emailPatient)
            ->subject('Confirmation de votre rendez-vous')
            ->html("
                <p>Bonjour,</p>
                <p>Votre rendez-vous du <strong>{$rendezVous->getDate()->format('Y-m-d H:i')}</strong> a été confirmé.</p>
                <p>Le prix de la consultation est de <strong>{$facture->getPrix()}D</strong>.</p>
                <p>Cordialement,</p>
                <p>Votre clinique</p>
            ");

        $mailer->send($email);
       
        


        $this->addFlash('success', 'Le rendez-vous a été confirmé et un e-mail a été envoyé.');
       

        return $this->redirectToRoute('app_admin_rendez_vous_index');
    }




    
}