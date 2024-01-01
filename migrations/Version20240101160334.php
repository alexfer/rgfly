<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101160334 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT fk_99f3ffc45aa1164f');
        $this->addSql('DROP SEQUENCE market_payment_method_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_payment_gateway_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_payment_gateway (id INT NOT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE market_payment_method');
        $this->addSql('DROP INDEX uniq_99f3ffc45aa1164f');
        $this->addSql('ALTER TABLE market_invoice RENAME COLUMN payment_method_id TO payment_gateway_id');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC462890FD5 FOREIGN KEY (payment_gateway_id) REFERENCES market_payment_gateway (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99F3FFC462890FD5 ON market_invoice (payment_gateway_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC462890FD5');
        $this->addSql('DROP SEQUENCE market_payment_gateway_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_payment_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_payment_method (id INT NOT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE market_payment_gateway');
        $this->addSql('DROP INDEX UNIQ_99F3FFC462890FD5');
        $this->addSql('ALTER TABLE market_invoice RENAME COLUMN payment_gateway_id TO payment_method_id');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT fk_99f3ffc45aa1164f FOREIGN KEY (payment_method_id) REFERENCES market_payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX uniq_99f3ffc45aa1164f ON market_invoice (payment_method_id)');
    }
}
