<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231028055942 extends AbstractMigration
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
        $this->addSql('DROP INDEX content ON entry_details');
        $this->addSql('CREATE FULLTEXT INDEX IDX_5EC0A41DFEC530A9 ON entry_details (content)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE contact CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX idx_5ec0a41dfec530a9 ON entry_details');
        $this->addSql('CREATE FULLTEXT INDEX content ON entry_details (content)');
    }
}
