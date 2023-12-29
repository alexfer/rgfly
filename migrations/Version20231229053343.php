<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229053343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE market_provider_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE market_product_provider_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_brand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE market_product_brand_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_brand (id INT NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D77C462D622F3F37 ON market_brand (market_id)');
        $this->addSql('CREATE TABLE market_product_brand (id INT NOT NULL, product_id INT DEFAULT NULL, provider_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CE09F354584665A ON market_product_brand (product_id)');
        $this->addSql('CREATE INDEX IDX_2CE09F35A53A8AA ON market_product_brand (provider_id)');
        $this->addSql('ALTER TABLE market_brand ADD CONSTRAINT FK_D77C462D622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT FK_2CE09F354584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT FK_2CE09F35A53A8AA FOREIGN KEY (provider_id) REFERENCES market_brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_provider DROP CONSTRAINT fk_c403355e4584665a');
        $this->addSql('ALTER TABLE market_product_provider DROP CONSTRAINT fk_c403355ea53a8aa');
        $this->addSql('ALTER TABLE market_provider DROP CONSTRAINT fk_7f756654622f3f37');
        $this->addSql('DROP TABLE market_product_provider');
        $this->addSql('DROP TABLE market_provider');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_brand_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE market_product_brand_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE market_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE market_product_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_provider (id INT NOT NULL, product_id INT DEFAULT NULL, provider_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_c403355ea53a8aa ON market_product_provider (provider_id)');
        $this->addSql('CREATE UNIQUE INDEX uniq_c403355e4584665a ON market_product_provider (product_id)');
        $this->addSql('CREATE TABLE market_provider (id INT NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX idx_7f756654622f3f37 ON market_provider (market_id)');
        $this->addSql('ALTER TABLE market_product_provider ADD CONSTRAINT fk_c403355e4584665a FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_provider ADD CONSTRAINT fk_c403355ea53a8aa FOREIGN KEY (provider_id) REFERENCES market_provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_provider ADD CONSTRAINT fk_7f756654622f3f37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_brand DROP CONSTRAINT FK_D77C462D622F3F37');
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT FK_2CE09F354584665A');
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT FK_2CE09F35A53A8AA');
        $this->addSql('DROP TABLE market_brand');
        $this->addSql('DROP TABLE market_product_brand');
    }
}
