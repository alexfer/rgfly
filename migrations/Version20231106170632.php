<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106170632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE category ADD slug VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('ALTER TABLE contact CHANGE status status ENUM(\'New\', \'Draft\', \'Answered\', \'Error\', \'Trashed\')');
        $this->addSql('ALTER TABLE entry ADD slug VARCHAR(255) DEFAULT NULL, CHANGE type type ENUM(\'blog\', \'article\', \'news\', \'other\'), CHANGE status status ENUM(\'draft\', \'published\', \'trashed\')');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B219D70989D9B62 ON entry (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_64C19C1989D9B62 ON category');
        $this->addSql('ALTER TABLE category DROP slug');
        $this->addSql('ALTER TABLE contact CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('DROP INDEX UNIQ_2B219D70989D9B62 ON entry');
        $this->addSql('ALTER TABLE entry DROP slug, CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
