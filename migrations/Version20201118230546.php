<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201118230546 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE currency (id UUID NOT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN currency.id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE customer (id UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_81398e09e7927c74 ON customer (email)');
        $this->addSql('COMMENT ON COLUMN customer.id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE vendor_plan (id UUID NOT NULL, vendor_id UUID NOT NULL, currency_id UUID DEFAULT NULL, questionnaire_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, price INT NOT NULL, duration VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, is_approval_required BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_f7975ea2f603ee73 ON vendor_plan (vendor_id)');
        $this->addSql('CREATE INDEX idx_f7975ea2ce07e8ff ON vendor_plan (questionnaire_id)');
        $this->addSql('CREATE INDEX idx_f7975ea238248176 ON vendor_plan (currency_id)');
        $this->addSql('COMMENT ON COLUMN vendor_plan.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_plan.vendor_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_plan.currency_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_plan.questionnaire_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN vendor_plan.duration IS \'(DC2Type:dateinterval)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE photo_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN photo_type.id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE vendor (id UUID NOT NULL, name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, roles JSON NOT NULL, slug VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_f52233f6e7927c74 ON vendor (email)');
        $this->addSql('COMMENT ON COLUMN vendor.id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE customer_measurement (id UUID NOT NULL, customer_id UUID NOT NULL, notes TEXT NOT NULL, taken_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_2fabf3199395c3f3 ON customer_measurement (customer_id)');
        $this->addSql('COMMENT ON COLUMN customer_measurement.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_measurement.customer_id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE subscription (id UUID NOT NULL, customer_id UUID DEFAULT NULL, vendor_plan_id UUID DEFAULT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, is_approved BOOLEAN DEFAULT NULL, review_notes TEXT DEFAULT NULL, reviewed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_a3c664d3f6ee3af4 ON subscription (vendor_plan_id)');
        $this->addSql('CREATE INDEX idx_a3c664d39395c3f3 ON subscription (customer_id)');
        $this->addSql('COMMENT ON COLUMN subscription.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN subscription.vendor_plan_id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE customer_measurement_item (id UUID NOT NULL, customer_measurement_id UUID NOT NULL, measurement_type_id UUID NOT NULL, measurement VARCHAR(255) NOT NULL, unit VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7c2c9353b2395fe ON customer_measurement_item (customer_measurement_id)');
        $this->addSql('CREATE INDEX idx_7c2c93538b4cc8fe ON customer_measurement_item (measurement_type_id)');
        $this->addSql('COMMENT ON COLUMN customer_measurement_item.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_measurement_item.customer_measurement_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_measurement_item.measurement_type_id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE question (id UUID NOT NULL, questionnaire_id UUID NOT NULL, order_num INT NOT NULL, question TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_b6f7494ece07e8ff ON question (questionnaire_id)');
        $this->addSql('COMMENT ON COLUMN question.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN question.questionnaire_id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE questionnaire (id UUID NOT NULL, vendor_id UUID NOT NULL, title VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7a64daff603ee73 ON questionnaire (vendor_id)');
        $this->addSql('COMMENT ON COLUMN questionnaire.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN questionnaire.vendor_id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE measurement_type (id UUID NOT NULL, name VARCHAR(255) NOT NULL, slug VARCHAR(255) NOT NULL, units VARCHAR(255) NOT NULL, category VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN measurement_type.id IS \'(DC2Type:uuid)\'');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE customer_photo (id UUID NOT NULL, customer_id UUID NOT NULL, photo_type_id UUID DEFAULT NULL, title VARCHAR(255) DEFAULT NULL, description TEXT DEFAULT NULL, filename VARCHAR(255) DEFAULT NULL, taken_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_da82f123f0ac9b1e ON customer_photo (photo_type_id)');
        $this->addSql('CREATE INDEX idx_da82f1239395c3f3 ON customer_photo (customer_id)');
        $this->addSql('COMMENT ON COLUMN customer_photo.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_photo.customer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN customer_photo.photo_type_id IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE currency');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE vendor_plan');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE photo_type');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE vendor');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer_measurement');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE subscription');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer_measurement_item');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE question');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE questionnaire');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE measurement_type');
        $this->abortIf('postgresql' !== $this->connection->getDatabasePlatform()->getName(), 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP TABLE customer_photo');
    }
}
