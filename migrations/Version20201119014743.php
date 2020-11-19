<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use App\Core\Doctrine\Migrations\CoreMigration;
use App\Core\Entity\Currency;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\ORM\EntityManagerInterface;

final class Version20201119014743 extends CoreMigration
{
    public function up(Schema $schema): void
    {
        $this->loadCurrencies();
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }

    private function loadCurrencies(): void
    {
        foreach ($this->getCurrenciesData() as [$name, $code]) {
            $currency = new Currency();
            $currency->setName($name);
            $currency->setCode($code);

            $this->getService(EntityManagerInterface::class)->persist($currency);
        }

        $this->getService(EntityManagerInterface::class)->flush();
    }

    private function getCurrenciesData(): array
    {
        return [
            ['Real', 'BRL'],
            ['Euro', 'EUR'],
            ['US Dollars', 'USD'],
        ];
    }
}
