<?php

namespace App\Command;

use App\Service\MedicationReminderService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:send-medication-reminders',
    description: 'Envoie des rappels pour les médicaments à prendre prochainement',
)]
class SendMedicationRemindersCommand extends Command
{
    private MedicationReminderService $reminderService;

    public function __construct(MedicationReminderService $reminderService)
    {
        parent::__construct();
        $this->reminderService = $reminderService;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('Vérification des médicaments à prendre...');

        try {
            $this->reminderService->checkUpcomingMedications();
            $output->writeln('Les rappels ont été envoyés avec succès !');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Une erreur est survenue : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
