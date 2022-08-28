<?php

namespace App\Services;

use App\Entity\Machine;
use App\Entity\Process;
use App\Repository\MachineRepository;
use App\Repository\ProcessRepository;
use Exception;

class BalancerService
{

    public function __construct(private MachineRepository $machineRepository, private ProcessRepository $processRepository)
    {
    }

    /**
     * @throws Exception
     */
    public function addProcess(int $cpu, int $memory, string $name): void
    {
        $machines = $this->machineRepository->findAll();
        $machine = $this->selectMachineToProcess($cpu, $memory, $machines);

        $process = new Process();
        $process->setMachineId($machine);
        $process->setCpu($cpu);
        $process->setMemory($memory);
        $process->setName($name);

        $this->processRepository->add($process, true);
    }

    public function deleteProcess(int $id): void
    {
        $process = $this->processRepository->find($id);
        if (!$process) {
            throw new Exception('Process not found');
        }
        $this->processRepository->remove($process, true);

    }

    public function addMachine(int $cpu, int $memory): void
    {
        $machine = new Machine();
        $machine->setCpu($cpu);
        $machine->setMemory($memory);
        $this->machineRepository->add($machine, true);

        $this->rebalance();
    }

    public function deleteMachine(int $id): void
    {
        $machineForDelete = $this->machineRepository->find($id);
        $this->rebalance($id);
        $this->machineRepository->remove($machineForDelete, true);
    }

    /**
     * @param Machine[] $machines
     * @throws Exception
     */
    private function selectMachineToProcess(int $cpu,int $memory, array $machines): Machine
    {
        $candidates = [];

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

    /**
     * @throws Exception
     */
    public function rebalance(?int $machineForDelete = null): void
    {
        $machines = $this->machineRepository->findAll();
        $processes = [];

        foreach ($machines as $index => $machine) {
            foreach ($machine->getProcesses() as $process) {
                $machine->removeProcess($process);
                $processes[] = $process;
            }

            if ($machineForDelete && $machine->getId() === $machineForDelete) {
                unset($machines[$index]);
            }
        }

        /** @var Process[] $processes */
        foreach ($processes as $process) {
            $newMachine = $this->selectMachineToProcess($process->getCpu(), $process->getMemory(), $machines);
            $newMachine->addProcess($process);
            $this->processRepository->add($process, true);
        }
    }
}