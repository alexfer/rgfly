<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240104051844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_category ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_category RENAME COLUMN position TO level');
        $this->addSql('ALTER TABLE market_category ADD CONSTRAINT FK_EBFD0C09727ACA70 FOREIGN KEY (parent_id) REFERENCES market_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EBFD0C09727ACA70 ON market_category (parent_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_category DROP CONSTRAINT FK_EBFD0C09727ACA70');
        $this->addSql('DROP INDEX IDX_EBFD0C09727ACA70');
        $this->addSql('ALTER TABLE market_category DROP parent_id');
        $this->addSql('ALTER TABLE market_category RENAME COLUMN level TO "position"');
    }
}
