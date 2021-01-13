<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210113103837 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription ADD is_recurring BOOLEAN DEFAULT \'true\' NOT NULL');
        $this->addSql('ALTER TABLE vendor ADD photo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER INDEX idx_4ecfaf42f603ee73 RENAME TO IDX_2839672CF603EE73');
        $this->addSql('ALTER TABLE vendor_plan ADD is_recurring BOOLEAN DEFAULT \'true\' NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER INDEX idx_2839672cf603ee73 RENAME TO idx_4ecfaf42f603ee73');
        $this->addSql('ALTER TABLE vendor_plan DROP is_recurring');
        $this->addSql('ALTER TABLE vendor DROP photo');
        $this->addSql('ALTER TABLE subscription DROP is_recurring');
    }
}
