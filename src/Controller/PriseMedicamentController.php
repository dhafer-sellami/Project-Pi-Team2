<?php

namespace App\Controller;

use App\Entity\PriseMedicament;
use App\Form\PriseMedicamentType;
use App\Repository\PriseMedicamentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/prise/medicament')]
final class PriseMedicamentController extends AbstractController
{
    #[Route(name: 'app_prise_medicament_index', methods: ['GET'])]
    public function index(PriseMedicamentRepository $priseMedicamentRepository): Response
    {
        return $this->render('prise_medicament/index.html.twig', [
            'prise_medicaments' => $priseMedicamentRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_prise_medicament_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $priseMedicament = new PriseMedicament();
        $form = $this->createForm(PriseMedicamentType::class, $priseMedicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($priseMedicament);
            $entityManager->flush();

            return $this->redirectToRoute('app_prise_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prise_medicament/new.html.twig', [
            'prise_medicament' => $priseMedicament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prise_medicament_show', methods: ['GET'])]
    public function show(PriseMedicament $priseMedicament): Response
    {
        return $this->render('prise_medicament/show.html.twig', [
            'prise_medicament' => $priseMedicament,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_prise_medicament_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, PriseMedicament $priseMedicament, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PriseMedicamentType::class, $priseMedicament);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_prise_medicament_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('prise_medicament/edit.html.twig', [
            'prise_medicament' => $priseMedicament,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_prise_medicament_delete', methods: ['POST'])]
    public function delete(Request $request, PriseMedicament $priseMedicament, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$priseMedicament->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($priseMedicament);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_prise_medicament_index', [], Response::HTTP_SEE_OTHER);
    }
}
