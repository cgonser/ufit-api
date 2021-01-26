<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210126123059 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD birth_date TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('ALTER TABLE customer ADD gender VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD height VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ADD goals JSON DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer DROP phone');
        $this->addSql('ALTER TABLE customer DROP birth_date');
        $this->addSql('ALTER TABLE customer DROP gender');
        $this->addSql('ALTER TABLE customer DROP height');
        $this->addSql('ALTER TABLE customer DROP goals');
    }
}
