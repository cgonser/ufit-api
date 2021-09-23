<?php

declare(strict_types=1);

namespace App\Program\MessageHandler;

use App\Program\Message\ProgramAssignmentCreatedEvent;
use App\Program\Provider\ProgramAssignmentProvider;
use App\Program\Service\ProgramEmailManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class ProgramAssignmentCreatedHandler implements MessageHandlerInterface
{
    public function __construct(
        private ProgramAssignmentProvider $programAssignmentProvider,
        private ProgramEmailManager $programEmailManager,
        private LoggerInterface $logger,
    ) {
    }

    public function __invoke(ProgramAssignmentCreatedEvent $programAssignmentCreatedEvent)
    {
        $programAssignment = $this->programAssignmentProvider->get(
            $programAssignmentCreatedEvent->getProgramAssignmentId()
        );

        $this->logger->info(
            ProgramAssignmentCreatedEvent::NAME,
            [
                'id' => $programAssignment->getId()
                    ->toString(),
            ]
        );

        $this->programEmailManager->sendAssignedEmail($programAssignment);
    }
}
