<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310171848 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_measurement ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_measurement ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_measurement ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_photo ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_photo ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE customer_photo ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT fk_9065174437ce34b3');
        $this->addSql('DROP INDEX idx_9065174437ce34b3');
        $this->addSql('ALTER TABLE invoice DROP curency_id');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174438248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE measurement_type ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE measurement_type ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE measurement_type ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_asset ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_assignment ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_assignment ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_assignment DROP assigned_at');
        $this->addSql('ALTER TABLE vendor ADD deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription_cycle ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription_cycle ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_asset DROP updated_at');
        $this->addSql('ALTER TABLE program_asset ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item DROP created_at');
        $this->addSql('ALTER TABLE customer_measurement_item DROP updated_at');
        $this->addSql('ALTER TABLE customer_measurement_item DROP deleted_at');
        $this->addSql('ALTER TABLE program_assignment ADD assigned_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE program_assignment DROP created_at');
        $this->addSql('ALTER TABLE program_assignment DROP updated_at');
        $this->addSql('ALTER TABLE vendor_plan ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_plan ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor DROP deleted_at');
        $this->addSql('ALTER TABLE vendor ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement DROP created_at');
        $this->addSql('ALTER TABLE customer_measurement DROP updated_at');
        $this->addSql('ALTER TABLE customer_measurement DROP deleted_at');
        $this->addSql('ALTER TABLE invoice DROP CONSTRAINT FK_9065174438248176');
        $this->addSql('ALTER TABLE invoice ADD curency_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE invoice ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER updated_at DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN invoice.curency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT fk_9065174437ce34b3 FOREIGN KEY (curency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_9065174437ce34b3 ON invoice (curency_id)');
        $this->addSql('ALTER TABLE measurement_type DROP created_at');
        $this->addSql('ALTER TABLE measurement_type DROP updated_at');
        $this->addSql('ALTER TABLE measurement_type DROP deleted_at');
        $this->addSql('ALTER TABLE customer_photo DROP created_at');
        $this->addSql('ALTER TABLE customer_photo DROP updated_at');
        $this->addSql('ALTER TABLE customer_photo DROP deleted_at');
        $this->addSql('ALTER TABLE questionnaire ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE questionnaire ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE question ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE question ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER updated_at DROP NOT NULL');
    }
}
