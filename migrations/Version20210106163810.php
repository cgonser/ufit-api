<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210106163810 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vendor_instagram_profile (id UUID NOT NULL, vendor_id UUID NOT NULL, instagram_id VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, access_token VARCHAR(255) NOT NULL, is_business BOOLEAN NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4ECFAF42F603EE73 ON vendor_instagram_profile (vendor_id)');
        $this->addSql('COMMENT ON COLUMN vendor_instagram_profile.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_instagram_profile.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vendor_instagram_profile ADD CONSTRAINT FK_4ECFAF42F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE vendor_instagram_profile');
    }
}
