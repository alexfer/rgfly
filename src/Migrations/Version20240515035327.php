<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515035327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_coupon ADD max_usage SMALLINT NOT NULL');
        $this->addSql('ALTER TABLE market_coupon ADD type VARCHAR(50) NOT NULL');
        $this->addSql('ALTER TABLE market_coupon ADD order_limit INT NOT NULL');
        $this->addSql('ALTER TABLE market_coupon ADD promotion_text VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_coupon DROP max_usage');
        $this->addSql('ALTER TABLE market_coupon DROP type');
        $this->addSql('ALTER TABLE market_coupon DROP order_limit');
        $this->addSql('ALTER TABLE market_coupon DROP promotion_text');
    }
}
