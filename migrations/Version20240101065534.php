<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101065534 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE market_iinvoice_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_invoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE market_payment_method_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_invoice (id INT NOT NULL, orders_id INT DEFAULT NULL, payment_method VARCHAR(100) DEFAULT NULL, number VARCHAR(50) NOT NULL, tax DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99F3FFC4CFFE9AD6 ON market_invoice (orders_id)');
        $this->addSql('COMMENT ON COLUMN market_invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_payment_method (id INT NOT NULL, name VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC4CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_iinvoice DROP CONSTRAINT fk_4a3eb90bcffe9ad6');
        $this->addSql('DROP TABLE market_iinvoice');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_invoice_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE market_payment_method_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_iinvoice_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_iinvoice (id INT NOT NULL, orders_id INT DEFAULT NULL, payment_method VARCHAR(100) DEFAULT NULL, number VARCHAR(50) NOT NULL, tax DOUBLE PRECISION NOT NULL, amount DOUBLE PRECISION NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, payed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX uniq_4a3eb90bcffe9ad6 ON market_iinvoice (orders_id)');
        $this->addSql('COMMENT ON COLUMN market_iinvoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_iinvoice ADD CONSTRAINT fk_4a3eb90bcffe9ad6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC4CFFE9AD6');
        $this->addSql('DROP TABLE market_invoice');
        $this->addSql('DROP TABLE market_payment_method');
    }
}
