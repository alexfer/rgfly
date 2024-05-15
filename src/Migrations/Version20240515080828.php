<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515080828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE market_coupon_usage (id SERIAL NOT NULL, coupon_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B4D4A7CE66C5951B ON market_coupon_usage (coupon_id)');
        $this->addSql('CREATE INDEX IDX_B4D4A7CE9395C3F3 ON market_coupon_usage (customer_id)');
        $this->addSql('COMMENT ON COLUMN market_coupon_usage.used_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CE66C5951B FOREIGN KEY (coupon_id) REFERENCES market_coupon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CE9395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CE66C5951B');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CE9395C3F3');
        $this->addSql('DROP TABLE market_coupon_usage');
    }
}
