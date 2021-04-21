<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210421154223 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE payment_method ALTER countries_enabled TYPE jsonb');
        $this->addSql('ALTER TABLE payment_method ALTER countries_enabled DROP DEFAULT');
        $this->addSql('ALTER TABLE payment_method ALTER countries_disabled TYPE jsonb');
        $this->addSql('ALTER TABLE payment_method ALTER countries_disabled DROP DEFAULT');
        $this->addSql('ALTER TABLE program ALTER goals TYPE JSON');
        $this->addSql('ALTER TABLE program ALTER goals DROP DEFAULT');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE program ALTER goals TYPE jsonb');
        $this->addSql('ALTER TABLE program ALTER goals DROP DEFAULT');
        $this->addSql('ALTER TABLE payment_method ALTER countries_enabled TYPE JSON');
        $this->addSql('ALTER TABLE payment_method ALTER countries_enabled DROP DEFAULT');
        $this->addSql('ALTER TABLE payment_method ALTER countries_disabled TYPE JSON');
        $this->addSql('ALTER TABLE payment_method ALTER countries_disabled DROP DEFAULT');
    }
}
