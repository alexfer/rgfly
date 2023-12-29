<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231225055209 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_manufacturer (id INT NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B77B4092622F3F37 ON market_manufacturer (market_id)');
        $this->addSql('ALTER TABLE market_manufacturer ADD CONSTRAINT FK_B77B4092622F3F37 FOREIGN KEY (market_id) REFERENCES brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_manufacturer_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_manufacturer DROP CONSTRAINT FK_B77B4092622F3F37');
        $this->addSql('DROP TABLE market_manufacturer');
    }
}
