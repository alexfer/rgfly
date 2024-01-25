<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240125044137 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_product_attribute DROP CONSTRAINT fk_8f468b21622f3f37');
        $this->addSql('DROP INDEX idx_8f468b21622f3f37');
        $this->addSql('ALTER TABLE market_product_attribute RENAME COLUMN market_id TO product_id');
        $this->addSql('ALTER TABLE market_product_attribute ADD CONSTRAINT FK_8F468B214584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_8F468B214584665A ON market_product_attribute (product_id)');
        $this->addSql('ALTER TABLE market_product_attribute_value ALTER in_use DROP DEFAULT');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('ALTER TABLE market_product_attribute_value ALTER in_use SET DEFAULT 1');
        $this->addSql('ALTER TABLE market_product_attribute DROP CONSTRAINT FK_8F468B214584665A');
        $this->addSql('DROP INDEX IDX_8F468B214584665A');
        $this->addSql('ALTER TABLE market_product_attribute RENAME COLUMN product_id TO market_id');
        $this->addSql('ALTER TABLE market_product_attribute ADD CONSTRAINT fk_8f468b21622f3f37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_8f468b21622f3f37 ON market_product_attribute (market_id)');
    }
}
