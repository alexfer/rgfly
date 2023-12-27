<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227060323 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_product_manufacturer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE market_product_supplier_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_manufacturer (id INT NOT NULL, product_id INT DEFAULT NULL, manufacturer_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_93EA846B4584665A ON market_product_manufacturer (product_id)');
        $this->addSql('CREATE INDEX IDX_93EA846BA23B42D ON market_product_manufacturer (manufacturer_id)');
        $this->addSql('CREATE TABLE market_product_supplier (id INT NOT NULL, product_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDED2ABC4584665A ON market_product_supplier (product_id)');
        $this->addSql('CREATE INDEX IDX_CDED2ABC2ADD6D8C ON market_product_supplier (supplier_id)');
        $this->addSql('ALTER TABLE market_product_manufacturer ADD CONSTRAINT FK_93EA846B4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_manufacturer ADD CONSTRAINT FK_93EA846BA23B42D FOREIGN KEY (manufacturer_id) REFERENCES market_manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_supplier ADD CONSTRAINT FK_CDED2ABC4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_supplier ADD CONSTRAINT FK_CDED2ABC2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES market_supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_product_manufacturer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE market_product_supplier_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product_manufacturer DROP CONSTRAINT FK_93EA846B4584665A');
        $this->addSql('ALTER TABLE market_product_manufacturer DROP CONSTRAINT FK_93EA846BA23B42D');
        $this->addSql('ALTER TABLE market_product_supplier DROP CONSTRAINT FK_CDED2ABC4584665A');
        $this->addSql('ALTER TABLE market_product_supplier DROP CONSTRAINT FK_CDED2ABC2ADD6D8C');
        $this->addSql('DROP TABLE market_product_manufacturer');
        $this->addSql('DROP TABLE market_product_supplier');
    }
}
