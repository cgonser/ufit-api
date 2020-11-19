<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201119015515 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer_measurement ADD CONSTRAINT FK_2FABF3199395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_measurement_item ADD CONSTRAINT FK_7C2C9353B2395FE FOREIGN KEY (customer_measurement_id) REFERENCES customer_measurement (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_measurement_item ADD CONSTRAINT FK_7C2C93538B4CC8FE FOREIGN KEY (measurement_type_id) REFERENCES measurement_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_photo ADD CONSTRAINT FK_DA82F1239395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_photo ADD CONSTRAINT FK_DA82F123F0AC9B1E FOREIGN KEY (photo_type_id) REFERENCES photo_type (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE measurement_type DROP category');
        $this->addSql('ALTER TABLE question ADD CONSTRAINT FK_B6F7494ECE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE questionnaire ADD CONSTRAINT FK_7A64DAFF603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D39395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE subscription ADD CONSTRAINT FK_A3C664D3F6EE3AF4 FOREIGN KEY (vendor_plan_id) REFERENCES vendor_plan (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_plan ADD CONSTRAINT FK_F7975EA2F603EE73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_plan ADD CONSTRAINT FK_F7975EA2CE07E8FF FOREIGN KEY (questionnaire_id) REFERENCES questionnaire (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE vendor_plan ADD CONSTRAINT FK_F7975EA238248176 FOREIGN KEY (currency_id) REFERENCES currency (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_plan DROP CONSTRAINT FK_F7975EA2F603EE73');
        $this->addSql('ALTER TABLE vendor_plan DROP CONSTRAINT FK_F7975EA2CE07E8FF');
        $this->addSql('ALTER TABLE vendor_plan DROP CONSTRAINT FK_F7975EA238248176');
        $this->addSql('ALTER TABLE customer_measurement DROP CONSTRAINT FK_2FABF3199395C3F3');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D39395C3F3');
        $this->addSql('ALTER TABLE subscription DROP CONSTRAINT FK_A3C664D3F6EE3AF4');
        $this->addSql('ALTER TABLE customer_measurement_item DROP CONSTRAINT FK_7C2C9353B2395FE');
        $this->addSql('ALTER TABLE customer_measurement_item DROP CONSTRAINT FK_7C2C93538B4CC8FE');
        $this->addSql('ALTER TABLE question DROP CONSTRAINT FK_B6F7494ECE07E8FF');
        $this->addSql('ALTER TABLE questionnaire DROP CONSTRAINT FK_7A64DAFF603EE73');
        $this->addSql('ALTER TABLE measurement_type ADD category VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE customer_photo DROP CONSTRAINT FK_DA82F1239395C3F3');
        $this->addSql('ALTER TABLE customer_photo DROP CONSTRAINT FK_DA82F123F0AC9B1E');
    }
}
