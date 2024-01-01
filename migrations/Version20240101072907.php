<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101072907 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_orders_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_orders_product (id INT NOT NULL, orders_id INT DEFAULT NULL, product_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_737098F1CFFE9AD6 ON market_orders_product (orders_id)');
        $this->addSql('CREATE INDEX IDX_737098F14584665A ON market_orders_product (product_id)');
        $this->addSql('ALTER TABLE market_orders_product ADD CONSTRAINT FK_737098F1CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_orders_product ADD CONSTRAINT FK_737098F14584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product RENAME COLUMN price TO cost');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_orders_product_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_orders_product DROP CONSTRAINT FK_737098F1CFFE9AD6');
        $this->addSql('ALTER TABLE market_orders_product DROP CONSTRAINT FK_737098F14584665A');
        $this->addSql('DROP TABLE market_orders_product');
        $this->addSql('ALTER TABLE market_product RENAME COLUMN cost TO price');
    }
}
