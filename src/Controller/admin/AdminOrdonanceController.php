<?php

namespace App\Controller\admin;

use App\Entity\Ordonance;
use App\Form\OrdonanceType;
use App\Repository\OrdonanceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('admin/ordonance')]
final class AdminOrdonanceController extends AbstractController
{
    #[Route(name: 'app_admin_ordonance', methods: ['GET'])]
    public function index(OrdonanceRepository $ordonanceRepository): Response
    {
        return $this->render('admin_ordonance/index.html.twig', [
            'ordonances' => $ordonanceRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_admin_ordonance_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ordonance = new Ordonance();
        $form = $this->createForm(OrdonanceType::class, $ordonance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ordonance);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ordonance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ordonance/new.html.twig', [
            'ordonance' => $ordonance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ordonance_show', methods: ['GET'])]
    public function show(Ordonance $ordonance): Response
    {
        return $this->render('admin_ordonance/show.html.twig', [
            'ordonance' => $ordonance,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_ordonance_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ordonance $ordonance, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(OrdonanceType::class, $ordonance);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_ordonance', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin_ordonance/edit.html.twig', [
            'ordonance' => $ordonance,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_admin_ordonance_delete', methods: ['POST'])]
    public function delete(Request $request, Ordonance $ordonance, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ordonance->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ordonance);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_ordonance', [], Response::HTTP_SEE_OTHER);
    }
}
