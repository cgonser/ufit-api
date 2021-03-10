<?php

namespace App\Program\Service;

use App\Customer\Provider\CustomerProvider;
use App\Program\Entity\Program;
use App\Program\Entity\ProgramAssignment;
use App\Program\Message\ProgramAssignmentCreatedEvent;
use App\Program\Message\ProgramAssignmentDeletedEvent;
use App\Program\Message\ProgramAssignmentUpdatedEvent;
use App\Program\Repository\ProgramAssignmentRepository;
use App\Program\Request\ProgramAssignmentRequest;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Messenger\MessageBusInterface;

class ProgramAssignmentManager
{
    private ProgramAssignmentRepository $programAssignmentRepository;

    private CustomerProvider $customerProvider;

    private MessageBusInterface $messageBus;

    public function __construct(
        ProgramAssignmentRepository $programAssignmentRepository,
        CustomerProvider $customerProvider,
        MessageBusInterface $messageBus
    ) {
        $this->programAssignmentRepository = $programAssignmentRepository;
        $this->customerProvider = $customerProvider;
        $this->messageBus = $messageBus;
    }

    public function createFromRequest(Program $program, ProgramAssignmentRequest $programAssignmentRequest): ProgramAssignment
    {
        $programAssignment = new ProgramAssignment();
        $programAssignment->setProgram($program);

        $this->mapFromRequest($programAssignment, $programAssignmentRequest);

        $this->programAssignmentRepository->save($programAssignment);

        $this->messageBus->dispatch(new ProgramAssignmentCreatedEvent($programAssignment->getId()));

        return $programAssignment;
    }

    public function updateFromRequest(ProgramAssignment $programAssignment, ProgramAssignmentRequest $programAssignmentRequest)
    {
        $this->mapFromRequest($programAssignment, $programAssignmentRequest);

        $this->programAssignmentRepository->save($programAssignment);

        $this->messageBus->dispatch(new ProgramAssignmentUpdatedEvent($programAssignment->getId()));
    }

    public function delete(ProgramAssignment $programAssignment)
    {
        $this->programAssignmentRepository->delete($programAssignment);

        $this->messageBus->dispatch(new ProgramAssignmentDeletedEvent($programAssignment->getId()));
    }

    private function mapFromRequest(ProgramAssignment $programAssignment, ProgramAssignmentRequest $programAssignmentRequest)
    {
        if (null !== $programAssignmentRequest->customerId) {
            $customer = $this->customerProvider->get(Uuid::fromString($programAssignmentRequest->customerId));
            $programAssignment->setCustomer($customer);
        }

        if (null !== $programAssignmentRequest->expiresAt) {
            $programAssignment->setExpiresAt(
                \DateTime::createFromFormat(\DateTimeInterface::ISO8601, $programAssignmentRequest->expiresAt)
            );
        }

        if (null !== $programAssignmentRequest->isActive) {
            $programAssignment->setIsActive($programAssignmentRequest->isActive);
        }
    }
}
