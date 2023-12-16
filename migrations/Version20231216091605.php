<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231216091605 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_social (id INT NOT NULL, details_id INT DEFAULT NULL, facebook_profile VARCHAR(512) DEFAULT NULL, twitter_profile VARCHAR(512) DEFAULT NULL, instagram_profile VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1433FABABB1A0722 ON user_social (details_id)');
        $this->addSql('ALTER TABLE user_social ADD CONSTRAINT FK_1433FABABB1A0722 FOREIGN KEY (details_id) REFERENCES user_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('ALTER TABLE user_social DROP CONSTRAINT FK_1433FABABB1A0722');
        $this->addSql('DROP TABLE user_social');
    }
}
