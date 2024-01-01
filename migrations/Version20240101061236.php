<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101061236 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_orders (id INT NOT NULL, market_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, total DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C58B56E2622F3F37 ON market_orders (market_id)');
        $this->addSql('COMMENT ON COLUMN market_orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_orders ADD CONSTRAINT FK_C58B56E2622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_orders_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_orders DROP CONSTRAINT FK_C58B56E2622F3F37');
        $this->addSql('DROP TABLE market_orders');
    }
}
