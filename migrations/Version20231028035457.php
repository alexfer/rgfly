<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028035457 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact CHANGE status status ENUM(\'New\', \'Draft\', \'Answered\', \'Error\', \'Trashed\')');
        $this->addSql('ALTER TABLE entry CHANGE type type ENUM(\'blog\', \'article\', \'news\', \'other\'), CHANGE status status ENUM(\'draft\', \'published\', \'trashed\')');
        $this->addSql('CREATE INDEX idx ON entry (status, type)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx ON entry');
        $this->addSql('ALTER TABLE entry CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
