<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220201220358 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication ADD usr_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE publication ADD CONSTRAINT FK_AF3C677941AB162D FOREIGN KEY (usr_id_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_AF3C677941AB162D ON publication (usr_id_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE publication DROP FOREIGN KEY FK_AF3C677941AB162D');
        $this->addSql('DROP INDEX IDX_AF3C677941AB162D ON publication');
        $this->addSql('ALTER TABLE publication DROP usr_id_id, CHANGE publication publication VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE photo_url photo_url VARCHAR(255) DEFAULT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(180) NOT NULL COLLATE `utf8mb4_unicode_ci`, CHANGE roles roles LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:json)\', CHANGE password password VARCHAR(255) NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
