<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210304165308 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD documents JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE customer ALTER password DROP NOT NULL');
        $this->addSql('DROP INDEX idx_6d28840df603ee73');
        $this->addSql('DROP INDEX idx_6d28840d5aa1164f');
        $this->addSql('DROP INDEX idx_6d28840d62424e4e');
        $this->addSql('DROP INDEX idx_6d28840d9395c3f3');
        $this->addSql('ALTER TABLE payment ADD currency_id UUID NOT NULL');
        $this->addSql('ALTER TABLE payment ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ALTER customer_id SET NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER vendor_id SET NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER subscription_cycle_id SET NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER payment_method_id SET NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER status TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE payment ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE payment ALTER due_date DROP NOT NULL');
        $this->addSql('COMMENT ON COLUMN payment.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D9395C3F3 ON payment (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840DF603EE73 ON payment (vendor_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D62424E4E ON payment (subscription_cycle_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D5AA1164F ON payment (payment_method_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D38248176 ON payment (currency_id)');
        $this->addSql('ALTER TABLE subscription ALTER customer_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER vendor_plan_id SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_6D28840D9395C3F3');
        $this->addSql('DROP INDEX UNIQ_6D28840DF603EE73');
        $this->addSql('DROP INDEX UNIQ_6D28840D62424E4E');
        $this->addSql('DROP INDEX UNIQ_6D28840D5AA1164F');
        $this->addSql('DROP INDEX UNIQ_6D28840D38248176');
        $this->addSql('ALTER TABLE payment DROP currency_id');
        $this->addSql('ALTER TABLE payment DROP created_at');
        $this->addSql('ALTER TABLE payment DROP updated_at');
        $this->addSql('ALTER TABLE payment ALTER customer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER vendor_id DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER subscription_cycle_id DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER payment_method_id DROP NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER status TYPE TEXT');
        $this->addSql('ALTER TABLE payment ALTER status DROP DEFAULT');
        $this->addSql('ALTER TABLE payment ALTER due_date SET NOT NULL');
        $this->addSql('CREATE INDEX idx_6d28840df603ee73 ON payment (vendor_id)');
        $this->addSql('CREATE INDEX idx_6d28840d5aa1164f ON payment (payment_method_id)');
        $this->addSql('CREATE INDEX idx_6d28840d62424e4e ON payment (subscription_cycle_id)');
        $this->addSql('CREATE INDEX idx_6d28840d9395c3f3 ON payment (customer_id)');
        $this->addSql('ALTER TABLE customer DROP documents');
        $this->addSql('ALTER TABLE customer ALTER password SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER customer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER vendor_plan_id DROP NOT NULL');
    }
}
