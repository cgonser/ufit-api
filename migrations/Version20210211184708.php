<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210211184708 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vendor_plan ALTER price TYPE NUMERIC(11, 2)');
        $this->addSql('ALTER TABLE vendor_plan ALTER price DROP DEFAULT');
        $this->addSql('UPDATE vendor_plan SET price = price / 100');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_plan ALTER price TYPE INT');
        $this->addSql('ALTER TABLE vendor_plan ALTER price DROP DEFAULT');
        $this->addSql('UPDATE vendor_plan SET price = price * 100');
    }
}
