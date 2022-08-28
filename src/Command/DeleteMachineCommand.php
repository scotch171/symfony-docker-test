<?php

namespace App\Command;

use App\Services\BalancerService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'machine:delete', description: 'Delete work machine')]
class DeleteMachineCommand extends Command
{
    private const ID = 'id';

    public function __construct(private BalancerService $balancerService)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(self::ID, InputArgument::REQUIRED, 'Machine ID');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $id = $input->getArgument(self::ID);

        try {
            $this->balancerService->deleteMachine($id);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }
    }
}