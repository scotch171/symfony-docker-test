<?php

namespace App\Command;

use App\Services\BalancerService;
use Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'process:add', description: 'Add new process')]
class AddProcessCommand extends Command
{
    private const NAME = 'name';
    private const CPU = 'cpu';
    private const MEMORY = 'memory';

    public function __construct(private BalancerService $balancerService)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(self::NAME, InputArgument::REQUIRED, 'Process name');
        $this->addArgument(self::CPU, InputArgument::REQUIRED, 'CPU usage');
        $this->addArgument(self::MEMORY, InputArgument::REQUIRED, 'Memory usage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument(self::NAME);
        $cpu = $input->getArgument(self::CPU);
        $memory = $input->getArgument(self::MEMORY);

        try {
            $this->balancerService->addProcess($cpu, $memory, $name);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->write($e->getMessage());
            return Command::FAILURE;
        }
    }
}