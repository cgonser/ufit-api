<?php

namespace App\Program\MessageHandler;

use App\Program\Message\ProgramAssignmentCreatedEvent;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Service\ProgramEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProgramAssignmentCreatedHandler implements MessageHandlerInterface
{
    private ProgramAssignmentProvider $programAssignmentProvider;

    private ProgramEmailManager $programEmailManager;

    private LoggerInterface $logger;

    public function __construct(
        ProgramAssignmentProvider $programAssignmentProvider,
        ProgramEmailManager $programEmailManager,
        LoggerInterface $logger
    ) {
        $this->programAssignmentProvider = $programAssignmentProvider;
        $this->programEmailManager = $programEmailManager;
        $this->logger = $logger;
    }

    public function __invoke(ProgramAssignmentCreatedEvent $programAssignmentCreatedEvent)
    {
        $programAssignment = $this->programAssignmentProvider->get($programAssignmentCreatedEvent->getProgramAssignmentId());

        $this->logger->info(
            ProgramAssignmentCreatedEvent::NAME,
            [
                'id' => $programAssignment->getId()->toString(),
            ]
        );

        $this->programEmailManager->sendAssignedEmail($programAssignment);
    }
}
