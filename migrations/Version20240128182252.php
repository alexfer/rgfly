<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240128182252 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_customer_orders_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_customer_orders (id INT NOT NULL, customer_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F1B1DB3A9395C3F3 ON market_customer_orders (customer_id)');
        $this->addSql('CREATE INDEX IDX_F1B1DB3ACFFE9AD6 ON market_customer_orders (orders_id)');
        $this->addSql('ALTER TABLE market_customer_orders ADD CONSTRAINT FK_F1B1DB3A9395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_orders ADD CONSTRAINT FK_F1B1DB3ACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product ALTER sku SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('DROP SEQUENCE market_customer_orders_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_customer_orders DROP CONSTRAINT FK_F1B1DB3A9395C3F3');
        $this->addSql('ALTER TABLE market_customer_orders DROP CONSTRAINT FK_F1B1DB3ACFFE9AD6');
        $this->addSql('DROP TABLE market_customer_orders');
        $this->addSql('ALTER TABLE market_product ALTER sku DROP NOT NULL');
    }
}
