<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Core\Doctrine\Migrations\CoreMigration;
use App\Vendor\Request\VendorPlanRequest;
use App\Vendor\Request\VendorRequest;
use App\Vendor\Service\VendorPlanManager;
use App\Vendor\Service\VendorRequestManager;
use Doctrine\DBAL\Schema\Schema;

final class Version20201119021601 extends CoreMigration
{
    public function up(Schema $schema): void
    {
        $this->loadVendors();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function loadVendors(): void
    {
        foreach ($this->getVendorData() as [$name, $password, $email, $plans]) {
            $vendorRequest = new VendorRequest();
            $vendorRequest->name = $name;
            $vendorRequest->password = $password;
            $vendorRequest->email = $email;

            $vendor = $this->getService(VendorRequestManager::class)->create($vendorRequest);

            foreach ($plans as $plan) {
                $vendorPlanRequest = new VendorPlanRequest();
                $vendorPlanRequest->name = $plan['name'];
                $vendorPlanRequest->currency = $plan['currency'];
                $vendorPlanRequest->price = $plan['price'];
                $vendorPlanRequest->durationDays = $plan['durationDays'];
                $vendorPlanRequest->durationMonths = $plan['durationMonths'];
                $vendorPlanRequest->isApprovalRequired = $plan['isApprovalRequired'];

                $this->getService(VendorPlanManager::class)->create($vendor, $vendorPlanRequest);
            }
        }
    }

    private function getVendorData(): array
    {
        $plans = [
            [
                'currency' => 'USD',
                'name' => 'Weekly',
                'price' => 100,
                'durationDays' => 7,
                'durationMonths' => 0,
                'isApprovalRequired' => true,
            ],
            [
                'currency' => 'USD',
                'name' => 'Monthly',
                'price' => 300,
                'durationDays' => 0,
                'durationMonths' => 1,
                'isApprovalRequired' => true,
            ],
            [
                'currency' => 'USD',
                'name' => 'Yearly',
                'price' => 3000,
                'durationDays' => 0,
                'durationMonths' => 12,
                'isApprovalRequired' => true,
            ],
        ];

        return [
            ['Vendor 1', '123', 'vendor1@ufit.io', $plans],
            ['Vendor 2', '123', 'vendor2@ufit.io', $plans],
            ['Vendor 3', '123', 'vendor3@ufit.io', $plans],
        ];
    }
}
