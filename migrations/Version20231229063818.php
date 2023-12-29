<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231229063818 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT fk_2ce09f35a53a8aa');
        $this->addSql('DROP INDEX idx_2ce09f35a53a8aa');
        $this->addSql('ALTER TABLE market_product_brand RENAME COLUMN provider_id TO brand_id');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT FK_2CE09F3544F5D008 FOREIGN KEY (brand_id) REFERENCES market_brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_2CE09F3544F5D008 ON market_product_brand (brand_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT FK_2CE09F3544F5D008');
        $this->addSql('DROP INDEX IDX_2CE09F3544F5D008');
        $this->addSql('ALTER TABLE market_product_brand RENAME COLUMN brand_id TO provider_id');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT fk_2ce09f35a53a8aa FOREIGN KEY (provider_id) REFERENCES market_brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_2ce09f35a53a8aa ON market_product_brand (provider_id)');
    }
}
