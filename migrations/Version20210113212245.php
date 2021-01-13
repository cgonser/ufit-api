<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210113212245 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment_method (id UUID NOT NULL, name VARCHAR(255) NOT NULL, countries_enabled JSON NOT NULL, countries_disabled JSON NOT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN payment_method.id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE payment_method');
    }
}
