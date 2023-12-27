<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227054436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_product_provider_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_provider (id INT NOT NULL, product_id INT DEFAULT NULL, provider_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C403355E4584665A ON market_product_provider (product_id)');
        $this->addSql('CREATE INDEX IDX_C403355EA53A8AA ON market_product_provider (provider_id)');
        $this->addSql('ALTER TABLE market_product_provider ADD CONSTRAINT FK_C403355E4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_provider ADD CONSTRAINT FK_C403355EA53A8AA FOREIGN KEY (provider_id) REFERENCES market_provider (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_product_provider_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product_provider DROP CONSTRAINT FK_C403355E4584665A');
        $this->addSql('ALTER TABLE market_product_provider DROP CONSTRAINT FK_C403355EA53A8AA');
        $this->addSql('DROP TABLE market_product_provider');
    }
}
