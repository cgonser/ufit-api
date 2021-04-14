<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Core\Doctrine\Migrations\CoreMigration;
use App\Customer\Request\CustomerRequest;
use App\Customer\Service\CustomerRequestManager;
use Doctrine\DBAL\Schema\Schema;

final class Version20201118230821 extends CoreMigration
{
    public function up(Schema $schema): void
    {
        $this->loadCustomers();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function loadCustomers(): void
    {
        foreach ($this->getCustomerData() as [$name, $password, $email]) {
            $customerCreateRequest = new CustomerRequest();
            $customerCreateRequest->name = $name;
            $customerCreateRequest->email = $email;
            $customerCreateRequest->password = $password;

            $this->getService(CustomerRequestManager::class)->createFromRequest($customerCreateRequest);
        }
    }

    private function getCustomerData(): array
    {
        return [
            ['Customer 1', '123', 'customer1@ufit.io'],
            ['Customer 2', '123', 'customer2@ufit.io'],
            ['Customer 3', '123', 'customer3@ufit.io'],
        ];
    }
}
