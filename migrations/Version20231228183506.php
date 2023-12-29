<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231228183506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE brand ADD attach_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD address VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD phone VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD logo VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE brand ADD CONSTRAINT FK_6BAC85CBE784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAC85CBE784F8B7 ON brand (attach_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE brand DROP CONSTRAINT FK_6BAC85CBE784F8B7');
        $this->addSql('DROP INDEX UNIQ_6BAC85CBE784F8B7');
        $this->addSql('ALTER TABLE brand DROP attach_id');
        $this->addSql('ALTER TABLE brand DROP address');
        $this->addSql('ALTER TABLE brand DROP email');
        $this->addSql('ALTER TABLE brand DROP phone');
        $this->addSql('ALTER TABLE brand DROP logo');
    }
}
