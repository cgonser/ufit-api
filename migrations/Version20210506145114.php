<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506145114 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoice ADD subscription_cycle_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN invoice.subscription_cycle_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174462424E4E FOREIGN KEY (subscription_cycle_id) REFERENCES subscription_cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_9065174462424E4E ON invoice (subscription_cycle_id)');
        $this->addSql('ALTER TABLE payment ADD external_reference VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_cycle DROP CONSTRAINT fk_4abed0e02989f1fd');
        $this->addSql('DROP INDEX uniq_4abed0e02989f1fd');
        $this->addSql('ALTER TABLE subscription_cycle DROP invoice_id');
        $this->addSql('DROP INDEX idx_f8a04fd2f603ee73');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment DROP external_reference');
        $this->addSql('ALTER TABLE subscription_cycle ADD invoice_id UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN subscription_cycle.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_cycle ADD CONSTRAINT fk_4abed0e02989f1fd FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_4abed0e02989f1fd ON subscription_cycle (invoice_id)');
        $this->addSql('CREATE INDEX idx_f8a04fd2f603ee73 ON vendor_bank_account (vendor_id)');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174462424E4E');
        $this->addSql('DROP INDEX IDX_9065174462424E4E');
        $this->addSql('ALTER TABLE invoice DROP subscription_cycle_id');
    }
}
