<?php

namespace App\Controller;

use App\Service\RapportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rapports', name: 'rapports_')]
class RapportController extends AbstractController
{
    private $rapportService;

    public function __construct(RapportService $rapportService)
    {
        $this->rapportService = $rapportService;
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->render('rapport/index.html.twig');
    }

    #[Route('/mensuel', name: 'mensuel')]
    public function rapportMensuel(Request $request): Response
    {
        $mois = new \DateTime($request->query->get('mois', 'now'));
        $patient = $this->getUser();

        return $this->rapportService->genererRapportMensuel($patient, $mois);
    }

    #[Route('/telecharger/{annee}/{mois}', name: 'telecharger')]
    public function telechargerRapport(int $annee, int $mois): Response
    {
        $date = new \DateTime(sprintf('%d-%d-01', $annee, $mois));
        $patient = $this->getUser();

        return $this->rapportService->genererRapportMensuel($patient, $date);
    }
}
