<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240121075049 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_product_attribute_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE market_product_attribute_value_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_attribute (id INT NOT NULL, product_id INT DEFAULT NULL, name VARCHAR(100) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F468B214584665A ON market_product_attribute (product_id)');
        $this->addSql('CREATE TABLE market_product_attribute_value (id INT NOT NULL, attribute_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C99FB8E4B6E62EFA ON market_product_attribute_value (attribute_id)');
        $this->addSql('ALTER TABLE market_product_attribute ADD CONSTRAINT FK_8F468B214584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attribute_value ADD CONSTRAINT FK_C99FB8E4B6E62EFA FOREIGN KEY (attribute_id) REFERENCES market_product_attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('DROP SEQUENCE market_product_attribute_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE market_product_attribute_value_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product_attribute DROP CONSTRAINT FK_8F468B214584665A');
        $this->addSql('ALTER TABLE market_product_attribute_value DROP CONSTRAINT FK_C99FB8E4B6E62EFA');
        $this->addSql('DROP TABLE market_product_attribute');
        $this->addSql('DROP TABLE market_product_attribute_value');
    }
}
