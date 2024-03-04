<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240303172201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_wishlist_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_wishlist (id INT NOT NULL, customer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, market_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_71503FF99395C3F3 ON market_wishlist (customer_id)');
        $this->addSql('CREATE INDEX IDX_71503FF94584665A ON market_wishlist (product_id)');
        $this->addSql('CREATE INDEX IDX_71503FF9622F3F37 ON market_wishlist (market_id)');
        $this->addSql('COMMENT ON COLUMN market_wishlist.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF99395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF94584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF9622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_wishlist_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF99395C3F3');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF94584665A');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF9622F3F37');
        $this->addSql('DROP TABLE market_wishlist');
    }
}
