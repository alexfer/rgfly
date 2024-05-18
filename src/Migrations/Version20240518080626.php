<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518080626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_coupon ALTER price TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_invoice ALTER tax TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_invoice ALTER amount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_invoice ALTER amount DROP NOT NULL');
        $this->addSql('ALTER TABLE market_orders_product ALTER cost TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_product ALTER cost TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_product ALTER discount TYPE NUMERIC(10, 2)');
        $this->addSql('ALTER TABLE market_product ALTER fee TYPE NUMERIC(10, 2)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_product ALTER cost TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_product ALTER discount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_product ALTER fee TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_coupon ALTER price TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_orders_product ALTER cost TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_invoice ALTER tax TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_invoice ALTER amount TYPE DOUBLE PRECISION');
        $this->addSql('ALTER TABLE market_invoice ALTER amount SET NOT NULL');
    }
}
