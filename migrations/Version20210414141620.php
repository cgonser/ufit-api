<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210414141620 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vendor_password_reset_token (id UUID NOT NULL, vendor_id UUID NOT NULL, token VARCHAR(255) NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BEA31D0CF603EE73 ON vendor_password_reset_token (vendor_id)');
        $this->addSql('COMMENT ON COLUMN vendor_password_reset_token.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_password_reset_token.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vendor_password_reset_token ADD CONSTRAINT FK_BEA31D0CF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F52233F6989D9B62 ON vendor (slug)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE vendor_password_reset_token');
        $this->addSql('DROP INDEX UNIQ_F52233F6989D9B62');
    }
}
