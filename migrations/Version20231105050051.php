<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231105050051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, `order` INT NOT NULL, created_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entry_category (id INT AUTO_INCREMENT NOT NULL, entry_id INT DEFAULT NULL, category_id INT DEFAULT NULL, INDEX IDX_680BF989BA364942 (entry_id), INDEX IDX_680BF98912469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE entry_category ADD CONSTRAINT FK_680BF989BA364942 FOREIGN KEY (entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE entry_category ADD CONSTRAINT FK_680BF98912469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE contact CHANGE status status ENUM(\'New\', \'Draft\', \'Answered\', \'Error\', \'Trashed\')');
        $this->addSql('ALTER TABLE entry CHANGE type type ENUM(\'blog\', \'article\', \'news\', \'other\'), CHANGE status status ENUM(\'draft\', \'published\', \'trashed\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entry_category DROP FOREIGN KEY FK_680BF989BA364942');
        $this->addSql('ALTER TABLE entry_category DROP FOREIGN KEY FK_680BF98912469DE2');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE entry_category');
        $this->addSql('ALTER TABLE contact CHANGE status status VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE entry CHANGE type type VARCHAR(255) DEFAULT NULL, CHANGE status status VARCHAR(255) DEFAULT NULL');
    }
}
