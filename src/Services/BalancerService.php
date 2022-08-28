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

    public function addProcess(int $cpu, int $memory, string $name): void
    {
        $machine = $this->getMachineToProcess($cpu, $memory);

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
    }

    public function deleteMachine(): void
    {

    }

    /**
     * @return Machine[]
     */
    private function getMachines(): array
    {
        return $this->machineRepository->findAll();
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