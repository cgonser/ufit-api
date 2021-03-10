<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310173121 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("UPDATE customer_measurement SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE customer_measurement SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE customer_measurement ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE customer_measurement_item SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE customer_measurement_item SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE customer_measurement_item ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE customer_photo SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE customer_photo SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE customer_photo ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE customer_photo ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE invoice SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE invoice SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE invoice ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE measurement_type SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE measurement_type SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE measurement_type ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE measurement_type ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE payment SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE payment SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE payment ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE payment ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE program SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE program SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE program ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE program ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE program_asset SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE program_asset SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE program_asset ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE program_asset ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE program_assignment SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE program_assignment SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE program_assignment ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE program_assignment ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE question SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE question SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE question ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE question ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE questionnaire SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE questionnaire SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE questionnaire ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE questionnaire ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE subscription SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE subscription SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE subscription ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE subscription_cycle SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE subscription_cycle SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE subscription_cycle ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE subscription_cycle ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE vendor SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE vendor SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE vendor ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE vendor ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE vendor_instagram_profile SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE vendor_instagram_profile SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER updated_at SET NOT NULL');
        $this->addSql("UPDATE vendor_plan SET created_at = '2000-01-01' WHERE created_at IS NULL");
        $this->addSql("UPDATE vendor_plan SET updated_at = '2000-01-01' WHERE updated_at IS NULL");
        $this->addSql('ALTER TABLE vendor_plan ALTER created_at SET NOT NULL');
        $this->addSql('ALTER TABLE vendor_plan ALTER updated_at SET NOT NULL');
        $this->addSql('DROP INDEX uniq_9065174438248176');
        $this->addSql('DROP INDEX uniq_906517449a1887dc');
        $this->addSql('CREATE INDEX IDX_906517449A1887DC ON invoice (subscription_id)');
        $this->addSql('CREATE INDEX IDX_9065174438248176 ON invoice (currency_id)');
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
        $this->addSql('ALTER TABLE customer_measurement_item ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement_item ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_asset ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_asset ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_assignment ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE program_assignment ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_plan ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_plan ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_measurement ALTER updated_at DROP NOT NULL');
        $this->addSql('DROP INDEX IDX_906517449A1887DC');
        $this->addSql('DROP INDEX IDX_9065174438248176');
        $this->addSql('ALTER TABLE invoice ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE invoice ALTER updated_at DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX uniq_9065174438248176 ON invoice (currency_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_906517449a1887dc ON invoice (subscription_id)');
        $this->addSql('ALTER TABLE questionnaire ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE questionnaire ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE question ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE question ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE subscription ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_photo ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE customer_photo ALTER updated_at DROP NOT NULL');
        $this->addSql('ALTER TABLE measurement_type ALTER created_at DROP NOT NULL');
        $this->addSql('ALTER TABLE measurement_type ALTER updated_at DROP NOT NULL');
    }
}
