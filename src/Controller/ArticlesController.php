<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\ArticleFromType;


final class ArticlesController extends AbstractController{
    #[Route('/', name: 'app_articles')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }
    #[Route('/#book', name: 'book_appointment')]
public function bookAppointment(Request $request, ManagerRegistry $manager): Response
{
    $Artcles = new Artcles();

    $form = $this->createForm(ArticleFromType::class, $Artcles, [
        'validation_groups' => ['Default', 'Artcles']
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $manager->getManager();
        $em->persist($Artcles);
        $em->flush();

        $this->addFlash('success', 'Appointment booked successfully!');

        return $this->redirectToRoute('book_appointment');
    }
    return $this->render('base.html.twig', [
        'form' => $form->createView(),
    ]);
}
}
