<?php

namespace App\Command;

use App\Services\BalancerService;
use Cassandra\Exception\ValidationException;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AddProcess extends Command
{
    protected static $defaultName = 'process:add';
    protected static $defaultDescription = 'Add new process';

    private const NAME = 'name';
    private const CPU = 'cpu';
    private const MEMORY = 'memory';

    protected function configure()
    {
        $this->addArgument(self::NAME, InputArgument::REQUIRED, 'Process name');
        $this->addArgument(self::CPU, InputArgument::REQUIRED, 'CPU usage');
        $this->addArgument(self::MEMORY, InputArgument::REQUIRED, 'Memory usage');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $kernel = $this->getApplication()->getKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $balancer = new BalancerService($em);

        $name = $input->getArgument(self::NAME);
        $cpu = $input->getArgument(self::CPU);
        $memory = $input->getArgument(self::MEMORY);

        try {
            $balancer->addProcess($cpu, $memory, $name);
            return Command::SUCCESS;
        } catch (Exception $e) {
            $output->write($e->getMessage());
            return Command::FAILURE;
        }
    }

}