<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210331164427 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE program ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE program ALTER vendor_id SET NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE program DROP description');
        $this->addSql('ALTER TABLE program ALTER vendor_id DROP NOT NULL');
    }
}
