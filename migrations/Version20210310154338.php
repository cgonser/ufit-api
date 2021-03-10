<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310154338 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE invoice (id UUID NOT NULL, subscription_id UUID NOT NULL, curency_id UUID DEFAULT NULL, total_amount NUMERIC(11, 2) NOT NULL, currency_id UUID NOT NULL, due_date DATE DEFAULT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, overdue_notification_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_906517449A1887DC ON invoice (subscription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_9065174438248176 ON invoice (currency_id)');
        $this->addSql('CREATE INDEX IDX_9065174437CE34B3 ON invoice (curency_id)');
        $this->addSql('COMMENT ON COLUMN invoice.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.subscription_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.curency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN invoice.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_906517449A1887DC FOREIGN KEY (subscription_id) REFERENCES subscription (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE invoice ADD CONSTRAINT FK_9065174437CE34B3 FOREIGN KEY (curency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840d9395c3f3');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840df603ee73');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840d62424e4e');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT fk_6d28840d37ce34b3');
        $this->addSql('DROP INDEX uniq_6d28840df603ee73');
        $this->addSql('DROP INDEX idx_6d28840d37ce34b3');
        $this->addSql('DROP INDEX uniq_6d28840d38248176');
        $this->addSql('DROP INDEX uniq_6d28840d62424e4e');
        $this->addSql('DROP INDEX uniq_6d28840d9395c3f3');
        $this->addSql('ALTER TABLE payment ADD invoice_id UUID NOT NULL');
        $this->addSql('ALTER TABLE payment DROP customer_id');
        $this->addSql('ALTER TABLE payment DROP vendor_id');
        $this->addSql('ALTER TABLE payment DROP subscription_cycle_id');
        $this->addSql('ALTER TABLE payment DROP curency_id');
        $this->addSql('ALTER TABLE payment DROP overdue_notification_sent_at');
        $this->addSql('ALTER TABLE payment DROP currency_id');
        $this->addSql('COMMENT ON COLUMN payment.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT FK_6D28840D2989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6D28840D2989F1FD ON payment (invoice_id)');
        $this->addSql('ALTER TABLE program_assignment ALTER program_id SET NOT NULL');
        $this->addSql('ALTER TABLE program_assignment ALTER customer_id SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('DROP INDEX idx_4abed0e09a1887dc');
        $this->addSql('ALTER TABLE subscription_cycle ADD invoice_id UUID NOT NULL');
        $this->addSql('ALTER TABLE subscription_cycle ADD created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_cycle ADD updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_cycle DROP is_paid');
        $this->addSql('ALTER TABLE subscription_cycle ALTER subscription_id SET NOT NULL');
        $this->addSql('COMMENT ON COLUMN subscription_cycle.invoice_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE subscription_cycle ADD CONSTRAINT FK_4ABED0E02989F1FD FOREIGN KEY (invoice_id) REFERENCES invoice (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4ABED0E09A1887DC ON subscription_cycle (subscription_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4ABED0E02989F1FD ON subscription_cycle (invoice_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE payment DROP CONSTRAINT FK_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE subscription_cycle DROP CONSTRAINT FK_4ABED0E02989F1FD');
        $this->addSql('DROP TABLE invoice');
        $this->addSql('DROP INDEX UNIQ_4ABED0E09A1887DC');
        $this->addSql('DROP INDEX UNIQ_4ABED0E02989F1FD');
        $this->addSql('ALTER TABLE subscription_cycle ADD is_paid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE subscription_cycle DROP invoice_id');
        $this->addSql('ALTER TABLE subscription_cycle DROP created_at');
        $this->addSql('ALTER TABLE subscription_cycle DROP updated_at');
        $this->addSql('ALTER TABLE subscription_cycle ALTER subscription_id DROP NOT NULL');
        $this->addSql('CREATE INDEX idx_4abed0e09a1887dc ON subscription_cycle (subscription_id)');
        $this->addSql('DROP INDEX UNIQ_6D28840D2989F1FD');
        $this->addSql('ALTER TABLE payment ADD vendor_id UUID NOT NULL');
        $this->addSql('ALTER TABLE payment ADD subscription_cycle_id UUID NOT NULL');
        $this->addSql('ALTER TABLE payment ADD curency_id UUID DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD overdue_notification_sent_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL');
        $this->addSql('ALTER TABLE payment ADD currency_id UUID NOT NULL');
        $this->addSql('ALTER TABLE payment RENAME COLUMN invoice_id TO customer_id');
        $this->addSql('COMMENT ON COLUMN payment.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.subscription_cycle_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.curency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN payment.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840d9395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840df603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840d62424e4e FOREIGN KEY (subscription_cycle_id) REFERENCES subscription_cycle (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE payment ADD CONSTRAINT fk_6d28840d37ce34b3 FOREIGN KEY (curency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840df603ee73 ON payment (vendor_id)');
        $this->addSql('CREATE INDEX idx_6d28840d37ce34b3 ON payment (curency_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840d38248176 ON payment (currency_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840d62424e4e ON payment (subscription_cycle_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840d9395c3f3 ON payment (customer_id)');
        $this->addSql('ALTER TABLE program_assignment ALTER program_id DROP NOT NULL');
        $this->addSql('ALTER TABLE program_assignment ALTER customer_id DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription DROP created_at');
        $this->addSql('ALTER TABLE subscription DROP updated_at');
    }
}
