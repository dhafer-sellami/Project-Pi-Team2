<?php

namespace App\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Artcles;
use App\Form\ArticleFormType;



final class ArticlesController extends AbstractController
{
    #[Route('/home', name: 'app_articles_index')]
    public function index(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'ArticlesController',
        ]);
    }

    #[Route('/book', name: 'book_appointment')]
    public function bookAppointment(Request $request, ManagerRegistry $manager): Response
    {
        $artcles = new Artcles();

        $form = $this->createForm(ArticleFormType::class, $artcles, [
            'validation_groups' => ['Default', 'Artcles']
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $manager->getManager();
            $em->persist($artcles);
            $em->flush();

            $this->addFlash('success', 'Appointment booked successfully!');

            return $this->redirectToRoute('book_appointment');
        }

        return $this->render('book.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/articles', name: 'list_articles')]
    public function listArticles(ManagerRegistry $manager): Response
        {
        $entityManager = $manager->getManager();
        $artclesRepository = $entityManager->getRepository(Artcles::class);
        $artcles = $artclesRepository->findAll();

        return $this->render('ListeArticles.html.twig', [
            'artcles' => $artcles
        ]);
    }
    #[Route('/articles/delete/{id}', name: 'delete_article')]
    public function deleteArticle(int $id, ManagerRegistry $manager): Response
    {
        $entityManager = $manager->getManager();
        $article = $entityManager->getRepository(Artcles::class)->find($id);

        if (!$article) {
            $this->addFlash('error', 'Article not found.');
            return $this->redirectToRoute('list_articles');
        }

        $entityManager->remove($article);
        $entityManager->flush();

        $this->addFlash('success', 'Article deleted successfully.');

        return $this->redirectToRoute('list_articles');
    }
    #[Route('/articles/edit/{id}', name: 'edit_article')]
    public function editArticle(int $id, Request $request, ManagerRegistry $manager): Response
    {
        $entityManager = $manager->getManager();
        $article = $entityManager->getRepository(Artcles::class)->find($id);

        if (!$article) {
            $this->addFlash('error', 'Article not found.');
            return $this->redirectToRoute('list_articles');
        }

        $form = $this->createForm(ArticleFormType::class, $article);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Article updated successfully.');

            return $this->redirectToRoute('list_articles');
        }

        return $this->render('editArticle.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}