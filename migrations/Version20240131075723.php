<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240131075723 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_orders ALTER total TYPE NUMERIC(10, 0)');
        $this->addSql('ALTER TABLE market_orders ALTER total DROP NOT NULL');
        $this->addSql('ALTER TABLE market_product ALTER sku SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('ALTER TABLE market_product ALTER sku DROP NOT NULL');
        $this->addSql('ALTER TABLE market_orders ALTER total TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_orders ALTER total SET NOT NULL');
    }
}
