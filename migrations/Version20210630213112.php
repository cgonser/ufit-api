<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210630213112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('DROP INDEX uniq_4abed0e09a1887dc');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('CREATE UNIQUE INDEX uniq_4abed0e09a1887dc ON subscription_cycle (subscription_id)');
    }
}
