<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210310212054 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER instagram_id DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER username DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER access_token DROP NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER is_business DROP NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER instagram_id SET NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER username SET NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER access_token SET NOT NULL');
        $this->addSql('ALTER TABLE vendor_instagram_profile ALTER is_business SET NOT NULL');
    }
}
