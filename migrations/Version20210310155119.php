<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310155119 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql("UPDATE customer SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE customer SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE customer ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE customer ALTER updated_at SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer DROP deleted_at');
        $this->addSql('ALTER TABLE customer ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer ALTER updated_at DROP NOT NULL');
    }
}
