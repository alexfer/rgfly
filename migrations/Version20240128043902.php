<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128043902 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_customer ADD market_orders_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_customer ADD CONSTRAINT FK_6C889BC15AAC6141 FOREIGN KEY (market_orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6C889BC15AAC6141 ON market_customer (market_orders_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('ALTER TABLE market_customer DROP CONSTRAINT FK_6C889BC15AAC6141');
        $this->addSql('DROP INDEX IDX_6C889BC15AAC6141');
        $this->addSql('ALTER TABLE market_customer DROP market_orders_id');
    }
}
