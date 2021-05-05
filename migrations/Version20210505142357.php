<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210505142357 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vendor_bank_account DROP CONSTRAINT fk_f8a04fd2f603ee73');
        $this->addSql('ALTER TABLE vendor_bank_account ADD is_valid BOOLEAN DEFAULT NULL');
        $this->addSql('ALTER TABLE vendor_bank_account ALTER vendor_id SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_bank_account DROP is_valid');
        $this->addSql('ALTER TABLE vendor_bank_account ALTER vendor_id DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_bank_account ADD CONSTRAINT fk_f8a04fd2f603ee73 FOREIGN KEY (vendor_id) REFERENCES vendor (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
