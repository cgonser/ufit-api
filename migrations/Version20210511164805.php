<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511164805 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customer_social_network (id UUID NOT NULL, customer_id UUID NOT NULL, platform VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, access_token TEXT NOT NULL, details JSON NOT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_769266EB9395C3F3 ON customer_social_network (customer_id)');
        $this->addSql('COMMENT ON COLUMN customer_social_network.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_social_network.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE vendor_social_network (id UUID NOT NULL, vendor_id UUID NOT NULL, platform VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, access_token TEXT NOT NULL, details JSON NOT NULL, is_active BOOLEAN DEFAULT \'true\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_811CF79EF603EE73 ON vendor_social_network (vendor_id)');
        $this->addSql('COMMENT ON COLUMN vendor_social_network.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_social_network.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE customer_social_network ADD CONSTRAINT FK_769266EB9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_social_network ADD CONSTRAINT FK_811CF79EF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_settings DROP CONSTRAINT fk_3856ab34f603ee73');
        $this->addSql('DROP INDEX idx_3856ab34f603ee73');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE customer_social_network');
        $this->addSql('DROP TABLE vendor_social_network');
        $this->addSql('ALTER TABLE vendor_settings ADD CONSTRAINT fk_3856ab34f603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_3856ab34f603ee73 ON vendor_settings (vendor_id)');
    }
}
