<?php

namespace App\Command;

use App\Services\BalancerService;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DeleteProcess extends Command
{
    protected static $defaultName = 'process:delete';
    protected static $defaultDescription = 'Delete process';

    private const ID = 'id';

    public function __construct(private BalancerService $balancerService)
    {
        parent::__construct();
    }

    protected function configure()
    {
        $this->addArgument(self::ID, InputArgument::REQUIRED, 'Process id');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $id = $input->getArgument(self::ID);
        try {
            $this->balancerService->deleteProcess($id);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }
    }
}