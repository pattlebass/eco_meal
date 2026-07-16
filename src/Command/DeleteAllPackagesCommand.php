<?php

namespace App\Command;

use App\Repository\PackageRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:delete-all-packages',
    description: 'Deletes all packages (for the end of the day).',
)]
class DeleteAllPackagesCommand extends Command
{
    public function __construct(private readonly PackageRepository $packageRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        foreach ($this->packageRepository->findAll() as $package) {
            $output->writeln(sprintf(
                'Would delete: %s (%d)',
                $package->getName(),
                $package->getId()
            ));
        }

        return Command::SUCCESS;
    }
}
