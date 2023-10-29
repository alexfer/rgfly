<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231029181415 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, contact_id INT NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, created_at DATETIME NOT NULL, INDEX IDX_DADD4A25E7A1254A (contact_id), INDEX IDX_DADD4A25A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE attach (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, size INT DEFAULT NULL, created_at DATETIME DEFAULT NULL, INDEX IDX_DEC97C81A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE contact (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, status ENUM(\'New\', \'Draft\', \'Answered\', \'Error\', \'Trashed\'), answers INT NOT NULL, phone VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, message TEXT NOT NULL, email VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entry (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, type ENUM(\'blog\', \'article\', \'news\', \'other\'), status ENUM(\'draft\', \'published\', \'trashed\'), comments INT NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, deleted_at DATETIME DEFAULT NULL, INDEX IDX_2B219D70A76ED395 (user_id), INDEX idx (status, type), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entry_attachment (id INT AUTO_INCREMENT NOT NULL, attach_id INT DEFAULT NULL, details_id INT DEFAULT NULL, INDEX IDX_E1F59089E784F8B7 (attach_id), INDEX IDX_E1F59089BB1A0722 (details_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entry_details (id INT AUTO_INCREMENT NOT NULL, entry_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_5EC0A41DBA364942 (entry_id), FULLTEXT INDEX IDX_5EC0A41DFEC530A9 (content), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE faq (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, visible INT DEFAULT 0 NOT NULL, created_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_details (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, about TEXT DEFAULT NULL, date_birth DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_2A2B1580A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed DATETIME NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE attach ADD CONSTRAINT FK_DEC97C81A76ED395 FOREIGN KEY (user_id) REFERENCES user_details (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089E784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id)');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089BB1A0722 FOREIGN KEY (details_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE entry_details ADD CONSTRAINT FK_5EC0A41DBA364942 FOREIGN KEY (entry_id) REFERENCES entry (id)');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_details ADD CONSTRAINT FK_2A2B1580A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25E7A1254A');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25A76ED395');
        $this->addSql('ALTER TABLE attach DROP FOREIGN KEY FK_DEC97C81A76ED395');
        $this->addSql('ALTER TABLE entry DROP FOREIGN KEY FK_2B219D70A76ED395');
        $this->addSql('ALTER TABLE entry_attachment DROP FOREIGN KEY FK_E1F59089E784F8B7');
        $this->addSql('ALTER TABLE entry_attachment DROP FOREIGN KEY FK_E1F59089BB1A0722');
        $this->addSql('ALTER TABLE entry_details DROP FOREIGN KEY FK_5EC0A41DBA364942');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE user_details DROP FOREIGN KEY FK_2A2B1580A76ED395');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE attach');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE entry_attachment');
        $this->addSql('DROP TABLE entry_details');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_details');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}
