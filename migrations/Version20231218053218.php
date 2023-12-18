<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218053218 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_product_attach_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_product_attach (id INT NOT NULL, product_id INT DEFAULT NULL, attach_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EDFAD3024584665A ON market_product_attach (product_id)');
        $this->addSql('CREATE INDEX IDX_EDFAD302E784F8B7 ON market_product_attach (attach_id)');
        $this->addSql('ALTER TABLE market_product_attach ADD CONSTRAINT FK_EDFAD3024584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attach ADD CONSTRAINT FK_EDFAD302E784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product ADD slug VARCHAR(512) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_product_attach_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product_attach DROP CONSTRAINT FK_EDFAD3024584665A');
        $this->addSql('ALTER TABLE market_product_attach DROP CONSTRAINT FK_EDFAD302E784F8B7');
        $this->addSql('DROP TABLE market_product_attach');
        $this->addSql('ALTER TABLE market_product DROP slug');
    }
}
