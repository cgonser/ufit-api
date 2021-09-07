<?php

declare(strict_types=1);

namespace App\Program\Service;

use App\Program\Entity\Program;
use App\Program\Message\ProgramCreatedEvent;
use App\Program\Message\ProgramDeletedEvent;
use App\Program\Message\ProgramUpdatedEvent;
use App\Program\Repository\ProgramRepository;
use App\Program\Request\ProgramRequest;
use App\Vendor\Entity\Vendor;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramManager
{
    public function __construct(
        private ProgramRepository $programRepository,
        private MessageBusInterface $messageBus,
    ) {
    }

    public function clone(Program $originalProgram): Program
    {
        $program = clone $originalProgram;
        $program->setOriginalProgram($originalProgram);

        foreach ($originalProgram->getAssets() as $originalProgramAsset) {
            $program->addAsset(clone $originalProgramAsset);
        }

        $this->programRepository->save($program);

        return $program;
    }

    public function createFromRequest(Vendor $vendor, ProgramRequest $programRequest): Program
    {
        $program = new Program();
        $program->setVendor($vendor);

        $this->mapFromRequest($program, $programRequest);

        $this->programRepository->save($program);

        $this->messageBus->dispatch(new ProgramCreatedEvent($program->getId()));

        return $program;
    }

    public function updateFromRequest(Program $program, ProgramRequest $programRequest): void
    {
        $this->mapFromRequest($program, $programRequest);

        $this->programRepository->save($program);

        $this->messageBus->dispatch(new ProgramUpdatedEvent($program->getId()));
    }

    public function delete(Program $program): void
    {
        $this->programRepository->delete($program);

        $this->messageBus->dispatch(new ProgramDeletedEvent($program->getId()));
    }

    private function mapFromRequest(Program $program, ProgramRequest $programRequest): void
    {
        if ($programRequest->has('name')) {
            $program->setName($programRequest->name);
        }

        if ($programRequest->has('level')) {
            $program->setLevel($programRequest->level);
        }

        if ($programRequest->has('goals')) {
            $program->setGoals($programRequest->goals);
        }

        if ($programRequest->has('description')) {
            $program->setDescription($programRequest->description);
        }

        if ($programRequest->has('isTemplate')) {
            $program->setIsTemplate($programRequest->isTemplate);
        }

        if ($programRequest->has('isActive')) {
            $program->setIsActive($programRequest->isActive);
        }
    }
}
