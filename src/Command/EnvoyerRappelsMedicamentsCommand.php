<?php

namespace App\Command;

use App\Repository\PriseMedicamentRepository;
use App\Service\NotificationService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:envoyer-rappels-medicaments',
    description: 'Envoie des rappels pour les prises de médicaments à venir',
)]
class EnvoyerRappelsMedicamentsCommand extends Command
{
    private $priseMedicamentRepository;
    private $notificationService;

    public function __construct(
        PriseMedicamentRepository $priseMedicamentRepository,
        NotificationService $notificationService
    ) {
        parent::__construct();
        $this->priseMedicamentRepository = $priseMedicamentRepository;
        $this->notificationService = $notificationService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Début de l\'envoi des rappels de médicaments...');

        // Récupérer les prises de médicaments prévues dans les 30 prochaines minutes
        $maintenant = new \DateTime();
        $dansUneDemiHeure = (new \DateTime())->modify('+30 minutes');
        
        $prisesMedicaments = $this->priseMedicamentRepository->trouverPrisesAVenir(
            $maintenant,
            $dansUneDemiHeure
        );

        foreach ($prisesMedicaments as $prise) {
            $this->notificationService->envoyerRappelMedicament(
                $prise->getPatient(),
                $prise->getMedicament()->getNom(),
                $prise->getHeurePrevue()
            );

            $output->writeln(sprintf(
                'Rappel envoyé pour %s à %s',
                $prise->getPatient()->getNom(),
                $prise->getHeurePrevue()->format('H:i')
            ));
        }

        $output->writeln('Envoi des rappels terminé.');

        return Command::SUCCESS;
    }
}
