<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240512025648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE market_coupon (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, discount INT DEFAULT NULL, price DOUBLE PRECISION DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_441B940E622F3F37 ON market_coupon (market_id)');
        $this->addSql('COMMENT ON COLUMN market_coupon.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_coupon.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_coupon.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_coupon_market_product (market_coupon_id INT NOT NULL, market_product_id INT NOT NULL, PRIMARY KEY(market_coupon_id, market_product_id))');
        $this->addSql('CREATE INDEX IDX_771060B4F3976E8C ON market_coupon_market_product (market_coupon_id)');
        $this->addSql('CREATE INDEX IDX_771060B42B7A3246 ON market_coupon_market_product (market_product_id)');
        $this->addSql('ALTER TABLE market_coupon ADD CONSTRAINT FK_441B940E622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_market_product ADD CONSTRAINT FK_771060B4F3976E8C FOREIGN KEY (market_coupon_id) REFERENCES market_coupon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_market_product ADD CONSTRAINT FK_771060B42B7A3246 FOREIGN KEY (market_product_id) REFERENCES market_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_coupon DROP CONSTRAINT FK_441B940E622F3F37');
        $this->addSql('ALTER TABLE market_coupon_market_product DROP CONSTRAINT FK_771060B4F3976E8C');
        $this->addSql('ALTER TABLE market_coupon_market_product DROP CONSTRAINT FK_771060B42B7A3246');
        $this->addSql('DROP TABLE market_coupon');
        $this->addSql('DROP TABLE market_coupon_market_product');
    }
}
