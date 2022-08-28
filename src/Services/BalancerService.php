<?php

namespace App\Services;

use App\Entity\Machine;
use App\Entity\Process;
use Cassandra\Exception\ValidationException;
use Doctrine\ORM\EntityManager;
use Exception;

class BalancerService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addProcess(int $cpu, int $memory, string $name): void
    {
        $machine = $this->getMachineToProcess($cpu, $memory);

        $process = new Process();
        $process->setMachineId($machine);
        $process->setCpu($cpu);
        $process->setMemory($memory);
        $process->setName($name);

        $this->entityManager->persist($process);
        $this->entityManager->flush();
    }

    public function deleteProcess(): void
    {

    }

    public function addMachine(int $cpu, int $memory): void
    {
        $machine = new Machine();
        $machine->setCpu($cpu);
        $machine->setMemory($memory);
        $this->entityManager->persist($machine);
        $this->entityManager->flush();
    }

    public function deleteMachine(): void
    {

    }

    /**
     * @return array<Machine>
     */
    private function getMachines(): array
    {
        return $this->entityManager->getRepository(Machine::class)->findAll();
    }

    private function getMachineToProcess(int $cpu,int $memory): Machine
    {
        $candidates = [];
        $machines = $this->getMachines();

        foreach ($machines as $index => $machine) {
            [$freeCpu, $freeMemory] = $machine->getFreeCpuAndMemory();
            if ($freeCpu - $cpu && $freeMemory - $memory) {
                $candidates[$index] = $freeCpu + $freeMemory;
            }
        }

        if (!$candidates) {
            throw new Exception('Нет сервера с требуемыми мощностями для размещения процесса');
        }

        return $machines[array_keys($candidates, max($candidates))[0]];
    }
}