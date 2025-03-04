<?php

namespace App\Service;

use App\Entity\User;
use App\Repository\PriseMedicamentRepository;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;
use Twig\Environment;

class RapportService
{
    private $priseMedicamentRepository;
    private $twig;

    public function __construct(
        PriseMedicamentRepository $priseMedicamentRepository,
        Environment $twig
    ) {
        $this->priseMedicamentRepository = $priseMedicamentRepository;
        $this->twig = $twig;
    }

    public function genererRapportMensuel(User $patient, \DateTime $mois): Response
    {
        $debut = (clone $mois)->modify('first day of this month');
        $fin = (clone $mois)->modify('last day of this month');

        $prisesMedicaments = $this->priseMedicamentRepository->trouverPrisesParPeriode(
            $patient,
            $debut,
            $fin
        );

        $statistiques = $this->calculerStatistiques($prisesMedicaments);

        // Générer le PDF
        $html = $this->twig->render('rapport/mensuel.html.twig', [
            'patient' => $patient,
            'mois' => $mois,
            'prises' => $prisesMedicaments,
            'statistiques' => $statistiques
        ]);

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isPhpEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        return new Response(
            $dompdf->output(),
            Response::HTTP_OK,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => sprintf(
                    'attachment; filename="rapport_mensuel_%s_%s.pdf"',
                    $patient->getNom(),
                    $mois->format('Y-m')
                )
            ]
        );
    }

    private function calculerStatistiques(array $prisesMedicaments): array
    {
        $total = count($prisesMedicaments);
        $prisesATemps = 0;
        $prisesEnRetard = 0;
        $prisesManquees = 0;

        foreach ($prisesMedicaments as $prise) {
            if ($prise->getEstPrise()) {
                $delai = $prise->getHeurePrise()->diff($prise->getHeurePrevue())->i;
                if ($delai <= 15) {
                    $prisesATemps++;
                } else {
                    $prisesEnRetard++;
                }
            } else {
                $prisesManquees++;
            }
        }

        return [
            'total' => $total,
            'prisesATemps' => $prisesATemps,
            'prisesEnRetard' => $prisesEnRetard,
            'prisesManquees' => $prisesManquees,
            'tauxConformite' => $total > 0 ? ($prisesATemps / $total) * 100 : 0
        ];
    }
}
