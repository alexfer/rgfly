<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240521055434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE market_message (id SERIAL NOT NULL, market_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, priority VARCHAR(50) DEFAULT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_BF2BD8FF622F3F37 ON market_message (market_id)');
        $this->addSql('CREATE INDEX IDX_BF2BD8FF9395C3F3 ON market_message (customer_id)');
        $this->addSql('CREATE INDEX IDX_BF2BD8FF4584665A ON market_message (product_id)');
        $this->addSql('COMMENT ON COLUMN market_message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_message.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_message ADD CONSTRAINT FK_BF2BD8FF622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_message ADD CONSTRAINT FK_BF2BD8FF9395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_message ADD CONSTRAINT FK_BF2BD8FF4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_message DROP CONSTRAINT FK_BF2BD8FF622F3F37');
        $this->addSql('ALTER TABLE market_message DROP CONSTRAINT FK_BF2BD8FF9395C3F3');
        $this->addSql('ALTER TABLE market_message DROP CONSTRAINT FK_BF2BD8FF4584665A');
        $this->addSql('DROP TABLE market_message');
    }
}
