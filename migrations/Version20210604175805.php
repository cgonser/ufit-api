<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210604175805 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD phone_area_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD phone_intl_code VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer RENAME COLUMN phone TO phone_number');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer DROP phone_intl_code');
        $this->addSql('ALTER TABLE customer DROP phone_area_code');
        $this->addSql('ALTER TABLE customer DROP phone_number');
    }
}
