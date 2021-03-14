<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210313180455 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vendor_bank_account (id UUID NOT NULL, vendor_id UUID DEFAULT NULL, bank_code VARCHAR(255) DEFAULT NULL, agency_number VARCHAR(255) DEFAULT NULL, account_number VARCHAR(255) DEFAULT NULL, account_digit VARCHAR(255) DEFAULT NULL, owner_name VARCHAR(255) DEFAULT NULL, owner_document_type VARCHAR(255) DEFAULT NULL, owner_document_number VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F8A04FD2F603EE73 ON vendor_bank_account (vendor_id)');
        $this->addSql('COMMENT ON COLUMN vendor_bank_account.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_bank_account.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vendor_bank_account ADD CONSTRAINT FK_F8A04FD2F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE vendor_bank_account');
    }
}
