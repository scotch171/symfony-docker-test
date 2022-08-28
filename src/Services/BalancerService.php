<?php

namespace App\Services;

use App\Entity\Machine;
use Doctrine\ORM\EntityManager;

class BalancerService
{
    private EntityManager $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addProcess(): void
    {

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

    private function getMachines(): array
    {
        return [];
    }
}