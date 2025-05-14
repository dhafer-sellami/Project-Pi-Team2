<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminFactureController extends AbstractController
{
    #[Route('/admin/admin/facture', name: 'app_admin_admin_facture')]
    public function index(): Response
    {
        return $this->render('admin/admin_facture/index.html.twig', [
            'controller_name' => 'AdminFactureController',
        ]);
    }
}
