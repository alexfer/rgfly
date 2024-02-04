<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204155603 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_customer_message_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_customer_message (id INT NOT NULL, customer_id INT DEFAULT NULL, market_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, phone VARCHAR(100) DEFAULT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3717B8949395C3F3 ON market_customer_message (customer_id)');
        $this->addSql('CREATE INDEX IDX_3717B894622F3F37 ON market_customer_message (market_id)');
        $this->addSql('CREATE INDEX IDX_3717B894CFFE9AD6 ON market_customer_message (orders_id)');
        $this->addSql('COMMENT ON COLUMN market_customer_message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_customer_message.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B8949395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B894622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B894CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market ADD messages TEXT NOT NULL');
        $this->addSql('ALTER TABLE market DROP messges');
        $this->addSql('COMMENT ON COLUMN market.messages IS \'(DC2Type:simple_array)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_customer_message_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B8949395C3F3');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B894622F3F37');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B894CFFE9AD6');
        $this->addSql('DROP TABLE market_customer_message');
        $this->addSql('ALTER TABLE market ADD messges TEXT DEFAULT \'["email"]\'');
        $this->addSql('ALTER TABLE market DROP messages');
        $this->addSql('COMMENT ON COLUMN market.messges IS \'(DC2Type:simple_array)\'');
    }
}
