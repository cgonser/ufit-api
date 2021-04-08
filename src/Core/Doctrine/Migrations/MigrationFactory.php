<?php

namespace App\Core\Doctrine\Migrations;

use App\Customer\Service\CustomerService;
use App\Vendor\Service\VendorPlanManager;
use App\Vendor\Service\VendorService;
use Doctrine\DBAL\Connection;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class MigrationFactory implements \Doctrine\Migrations\Version\MigrationFactory
{
    private Connection $connection;

    private LoggerInterface $logger;

    private EntityManagerInterface $entityManager;

    private CustomerService $customerService;

    private VendorService $vendorService;

    private VendorPlanManager $vendorPlanService;

    public function __construct(
        Connection $connection,
        LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        CustomerService $customerService,
        VendorService $vendorService,
        VendorPlanManager $vendorPlanService
    ) {
        $this->connection = $connection;
        $this->logger = $logger;
        $this->customerService = $customerService;
        $this->vendorService = $vendorService;
        $this->entityManager = $entityManager;
        $this->vendorPlanService = $vendorPlanService;
    }

    public function createVersion(string $migrationClassName): AbstractMigration
    {
        $migration = new $migrationClassName(
            $this->connection,
            $this->logger
        );

        // or you can ommit this check
        if ($migration instanceof CoreMigration) {
            $migration->addService(EntityManagerInterface::class, $this->entityManager);
            $migration->addService(CustomerService::class, $this->customerService);
            $migration->addService(VendorService::class, $this->vendorService);
            $migration->addService(VendorPlanManager::class, $this->vendorPlanService);
        }

        return $migration;
    }
}
