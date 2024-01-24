<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124053103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_product_variants_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_variants (id INT NOT NULL, attribute_id INT DEFAULT NULL, product_id INT DEFAULT NULL, quantity INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E55F1523B6E62EFA ON market_product_variants (attribute_id)');
        $this->addSql('CREATE INDEX IDX_E55F15234584665A ON market_product_variants (product_id)');
        $this->addSql('ALTER TABLE market_product_variants ADD CONSTRAINT FK_E55F1523B6E62EFA FOREIGN KEY (attribute_id) REFERENCES market_product_attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_variants ADD CONSTRAINT FK_E55F15234584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('DROP SEQUENCE market_product_variants_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product_variants DROP CONSTRAINT FK_E55F1523B6E62EFA');
        $this->addSql('ALTER TABLE market_product_variants DROP CONSTRAINT FK_E55F15234584665A');
        $this->addSql('DROP TABLE market_product_variants');
    }
}
