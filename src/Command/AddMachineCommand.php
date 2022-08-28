<?php

namespace App\Command;

use App\Services\BalancerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'machine:add', description: 'Add new work machine to db')]
class AddMachineCommand extends Command
{
    private const CPU = 'cpu';
    private const MEMORY = 'memory';

    public function __construct(private BalancerService $balancerService)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(self::CPU, InputArgument::REQUIRED, 'CPU value');
        $this->addArgument(self::MEMORY, InputArgument::REQUIRED, 'Memory value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $cpu = (int)$input->getArgument(self::CPU);
        $memory = (int)$input->getArgument(self::MEMORY);
        $this->balancerService->addMachine($cpu, $memory);

        return Command::SUCCESS;
    }

}