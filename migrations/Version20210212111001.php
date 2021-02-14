<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210212111001 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE payment (id UUID NOT NULL, customer_id UUID DEFAULT NULL, vendor_id UUID DEFAULT NULL, subscription_cycle_id UUID DEFAULT NULL, payment_method_id UUID DEFAULT NULL, curency_id UUID DEFAULT NULL, status TEXT DEFAULT NULL, amount NUMERIC(11, 2) NOT NULL, due_date DATE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, overdue_notification_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6D28840D9395C3F3 ON payment (customer_id)');
        $this->addSql('CREATE INDEX IDX_6D28840DF603EE73 ON payment (vendor_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D62424E4E ON payment (subscription_cycle_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D5AA1164F ON payment (payment_method_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D37CE34B3 ON payment (curency_id)');
        $this->addSql('COMMENT ON COLUMN payment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.subscription_cycle_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.payment_method_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.curency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program (id UUID NOT NULL, vendor_id UUID DEFAULT NULL, name TEXT NOT NULL, level TEXT DEFAULT NULL, goals JSON DEFAULT NULL, is_template BOOLEAN DEFAULT \'false\' NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92ED7784F603EE73 ON program (vendor_id)');
        $this->addSql('COMMENT ON COLUMN program.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program_program_asset (program_id UUID NOT NULL, program_asset_id UUID NOT NULL, PRIMARY KEY(program_id, program_asset_id))');
        $this->addSql('CREATE INDEX IDX_398F39323EB8070A ON program_program_asset (program_id)');
        $this->addSql('CREATE INDEX IDX_398F393284EFE574 ON program_program_asset (program_asset_id)');
        $this->addSql('COMMENT ON COLUMN program_program_asset.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_program_asset.program_asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program_program_assignment (program_id UUID NOT NULL, program_assignment_id UUID NOT NULL, PRIMARY KEY(program_id, program_assignment_id))');
        $this->addSql('CREATE INDEX IDX_3BAB7CA33EB8070A ON program_program_assignment (program_id)');
        $this->addSql('CREATE INDEX IDX_3BAB7CA374DA1260 ON program_program_assignment (program_assignment_id)');
        $this->addSql('COMMENT ON COLUMN program_program_assignment.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_program_assignment.program_assignment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program_asset (id UUID NOT NULL, program_id UUID DEFAULT NULL, title VARCHAR(255) NOT NULL, filename VARCHAR(255) NOT NULL, type VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9CC629AA3EB8070A ON program_asset (program_id)');
        $this->addSql('COMMENT ON COLUMN program_asset.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_asset.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program_assignment (id UUID NOT NULL, program_id UUID DEFAULT NULL, customer_id UUID DEFAULT NULL, assigned_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_26FFB90E3EB8070A ON program_assignment (program_id)');
        $this->addSql('CREATE INDEX IDX_26FFB90E9395C3F3 ON program_assignment (customer_id)');
        $this->addSql('COMMENT ON COLUMN program_assignment.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_assignment.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_assignment.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE subscription_cycle (id UUID NOT NULL, subscription_id UUID DEFAULT NULL, price NUMERIC(11, 2) NOT NULL, is_paid BOOLEAN DEFAULT NULL, starts_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, ends_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_4ABED0E09A1887DC ON subscription_cycle (subscription_id)');
        $this->addSql('COMMENT ON COLUMN subscription_cycle.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription_cycle.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840DF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D62424E4E FOREIGN KEY (subscription_cycle_id) REFERENCES subscription_cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D37CE34B3 FOREIGN KEY (curency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program ADD CONSTRAINT FK_92ED7784F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_asset ADD CONSTRAINT FK_398F39323EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_asset ADD CONSTRAINT FK_398F393284EFE574 FOREIGN KEY (program_asset_id) REFERENCES program_asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_assignment ADD CONSTRAINT FK_3BAB7CA33EB8070A FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_assignment ADD CONSTRAINT FK_3BAB7CA374DA1260 FOREIGN KEY (program_assignment_id) REFERENCES program_assignment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_asset ADD CONSTRAINT FK_9CC629AA3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_assignment ADD CONSTRAINT FK_26FFB90E3EB8070A FOREIGN KEY (program_id) REFERENCES program (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_assignment ADD CONSTRAINT FK_26FFB90E9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription_cycle ADD CONSTRAINT FK_4ABED0E09A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER measurement TYPE NUMERIC(11, 2) USING measurement::numeric(11,2)');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER measurement DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE program_program_asset DROP CONSTRAINT FK_398F39323EB8070A');
        $this->addSql('ALTER TABLE program_program_assignment DROP CONSTRAINT FK_3BAB7CA33EB8070A');
        $this->addSql('ALTER TABLE program_asset DROP CONSTRAINT FK_9CC629AA3EB8070A');
        $this->addSql('ALTER TABLE program_assignment DROP CONSTRAINT FK_26FFB90E3EB8070A');
        $this->addSql('ALTER TABLE program_program_asset DROP CONSTRAINT FK_398F393284EFE574');
        $this->addSql('ALTER TABLE program_program_assignment DROP CONSTRAINT FK_3BAB7CA374DA1260');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D62424E4E');
        $this->addSql('DROP TABLE payment');
        $this->addSql('DROP TABLE program');
        $this->addSql('DROP TABLE program_program_asset');
        $this->addSql('DROP TABLE program_program_assignment');
        $this->addSql('DROP TABLE program_asset');
        $this->addSql('DROP TABLE program_assignment');
        $this->addSql('DROP TABLE subscription_cycle');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER measurement TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER measurement DROP DEFAULT');
    }
}
