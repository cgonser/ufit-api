<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210212230826 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE program_program_asset');
        $this->addSql('DROP TABLE program_program_assignment');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE TABLE program_program_asset (program_id UUID NOT NULL, program_asset_id UUID NOT NULL, PRIMARY KEY(program_id, program_asset_id))');
        $this->addSql('CREATE INDEX idx_398f393284efe574 ON program_program_asset (program_asset_id)');
        $this->addSql('CREATE INDEX idx_398f39323eb8070a ON program_program_asset (program_id)');
        $this->addSql('COMMENT ON COLUMN program_program_asset.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_program_asset.program_asset_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE program_program_assignment (program_id UUID NOT NULL, program_assignment_id UUID NOT NULL, PRIMARY KEY(program_id, program_assignment_id))');
        $this->addSql('CREATE INDEX idx_3bab7ca374da1260 ON program_program_assignment (program_assignment_id)');
        $this->addSql('CREATE INDEX idx_3bab7ca33eb8070a ON program_program_assignment (program_id)');
        $this->addSql('COMMENT ON COLUMN program_program_assignment.program_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN program_program_assignment.program_assignment_id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE program_program_asset ADD CONSTRAINT fk_398f39323eb8070a FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_asset ADD CONSTRAINT fk_398f393284efe574 FOREIGN KEY (program_asset_id) REFERENCES program_asset (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_assignment ADD CONSTRAINT fk_3bab7ca33eb8070a FOREIGN KEY (program_id) REFERENCES program (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE program_program_assignment ADD CONSTRAINT fk_3bab7ca374da1260 FOREIGN KEY (program_assignment_id) REFERENCES program_assignment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
