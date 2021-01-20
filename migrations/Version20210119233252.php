<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119233252 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vendor_plan_payment_method (vendor_plan_id UUID NOT NULL, payment_method_id UUID NOT NULL, PRIMARY KEY(vendor_plan_id, payment_method_id))');
        $this->addSql('CREATE INDEX IDX_7C479FDEF6EE3AF4 ON vendor_plan_payment_method (vendor_plan_id)');
        $this->addSql('CREATE INDEX IDX_7C479FDE5AA1164F ON vendor_plan_payment_method (payment_method_id)');
        $this->addSql('COMMENT ON COLUMN vendor_plan_payment_method.vendor_plan_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_plan_payment_method.payment_method_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE vendor_plan_payment_method ADD CONSTRAINT FK_7C479FDEF6EE3AF4 FOREIGN KEY (vendor_plan_id) REFERENCES vendor_plan (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_plan_payment_method ADD CONSTRAINT FK_7C479FDE5AA1164F FOREIGN KEY (payment_method_id) REFERENCES payment_method (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE vendor_plan_payment_method');
    }
}
