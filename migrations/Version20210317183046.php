<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210317183046 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql("UPDATE payment_method SET name = 'boleto' WHERE name = 'Boleto'");
        $this->addSql("UPDATE payment_method SET name = 'credit-card' WHERE name = 'Credit Card'");
    }

    public function down(Schema $schema) : void
    {
        $this->addSql("UPDATE payment_method SET name = 'Boleto' WHERE name = 'boleto'");
        $this->addSql("UPDATE payment_method SET name = 'Credit Card' WHERE name = 'credit-card'");
    }
}
