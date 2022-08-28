<?php

namespace App\Command;

use App\Entity\Machine;
use App\Services\BalancerService;
use Cassandra\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class AddMachine extends Command
{
    protected static $defaultName = 'machine:add';
    protected static $defaultDescription = 'Add new machine to db';

    private const CPU = 'cpu';
    private const MEMORY = 'memory';

    protected function configure()
    {
        $this->addArgument(self::CPU, InputArgument::REQUIRED, 'CPU value');
        $this->addArgument(self::MEMORY, InputArgument::REQUIRED, 'Memory value');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var KernelInterface $kernel */
        $kernel = $this->getApplication()->getKernel();
        $em = $kernel->getContainer()->get('doctrine')->getManager();

        $balancer = new BalancerService($em);
        $cpu = (int)$input->getArgument(self::CPU);
        $memory = (int)$input->getArgument(self::MEMORY);
        $balancer->addMachine($cpu, $memory);

        return Command::SUCCESS;
    }

}