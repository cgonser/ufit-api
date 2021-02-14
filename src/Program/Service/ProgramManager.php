<?php

namespace App\Program\Service;

use App\Program\Entity\Program;
use App\Program\Message\ProgramCreatedEvent;
use App\Program\Message\ProgramDeletedEvent;
use App\Program\Message\ProgramUpdatedEvent;
use App\Program\Repository\ProgramRepository;
use App\Program\Request\ProgramRequest;
use App\Vendor\Entity\Vendor;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramManager
{
    private ProgramRepository $programRepository;

    private MessageBusInterface $messageBus;

    public function __construct(
        ProgramRepository $programRepository,
        MessageBusInterface $messageBus
    ) {
        $this->programRepository = $programRepository;
        $this->messageBus = $messageBus;
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

    public function updateFromRequest(Program $program, ProgramRequest $programRequest)
    {
        $this->mapFromRequest($program, $programRequest);

        $this->messageBus->dispatch(new ProgramUpdatedEvent($program->getId()));

        $this->programRepository->save($program);
    }

    public function delete(Program $program)
    {
        $this->programRepository->delete($program);

        $this->messageBus->dispatch(new ProgramDeletedEvent($program->getId()));
    }

    private function mapFromRequest(Program $program, ProgramRequest $programRequest)
    {
        if (null !== $programRequest->name) {
            $program->setName($programRequest->name);
        }

        if (null !== $programRequest->level) {
            $program->setLevel($programRequest->level);
        }

        if (null !== $programRequest->isTemplate) {
            $program->setIsTemplate($programRequest->isTemplate);
        }

        if (null !== $programRequest->goals) {
            $program->setGoals($programRequest->goals);
        }
    }
}
