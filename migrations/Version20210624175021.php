<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210624175021 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE billing_information (id UUID NOT NULL, customer_id UUID NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, birth_date TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, phone_intl_code VARCHAR(255) DEFAULT NULL, phone_area_code VARCHAR(255) DEFAULT NULL, phone_number VARCHAR(255) DEFAULT NULL, document_type VARCHAR(255) DEFAULT NULL, document_number VARCHAR(255) DEFAULT NULL, address_line1 TEXT DEFAULT NULL, address_line2 VARCHAR(255) DEFAULT NULL, address_number VARCHAR(255) DEFAULT NULL, address_district VARCHAR(255) DEFAULT NULL, address_city VARCHAR(255) DEFAULT NULL, address_state VARCHAR(255) DEFAULT NULL, address_country VARCHAR(255) DEFAULT NULL, address_zip_code VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN billing_information.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN billing_information.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD billing_information_id UUID DEFAULT NULL');
        $this->addSql('COMMENT ON COLUMN payment.billing_information_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DA63509B6 FOREIGN KEY (billing_information_id) REFERENCES billing_information (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6D28840DA63509B6 ON payment (billing_information_id)');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840DA63509B6');
        $this->addSql('DROP TABLE billing_information');
        $this->addSql('DROP INDEX IDX_6D28840DA63509B6');
        $this->addSql('ALTER TABLE payment DROP billing_information_id');
    }
}
