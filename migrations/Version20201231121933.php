<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201231121933 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE question ALTER order_num DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_plan ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE vendor_plan ADD features JSON DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_plan DROP description');
        $this->addSql('ALTER TABLE vendor_plan DROP features');
        $this->addSql('ALTER TABLE question ALTER order_num SET NOT NULL');
    }
}
