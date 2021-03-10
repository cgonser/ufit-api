<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310175627 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_6d28840d5aa1164f');
        $this->addSql('DROP INDEX uniq_6d28840d2989f1fd');
        $this->addSql('CREATE INDEX IDX_6D28840D2989F1FD ON payment (invoice_id)');
        $this->addSql('CREATE INDEX IDX_6D28840D5AA1164F ON payment (payment_method_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX IDX_6D28840D2989F1FD');
        $this->addSql('DROP INDEX IDX_6D28840D5AA1164F');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840d5aa1164f ON payment (payment_method_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_6d28840d2989f1fd ON payment (invoice_id)');
    }
}
