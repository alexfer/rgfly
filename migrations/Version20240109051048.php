<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240109051048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_customer (id INT NOT NULL, customer_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, birthday DATE DEFAULT NULL, gender VARCHAR(10) DEFAULT NULL, phone VARCHAR(30) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C889BC19395C3F3 ON market_customer (customer_id)');
        $this->addSql('COMMENT ON COLUMN market_customer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_customer.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_customer.deleted_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE market_customer ADD CONSTRAINT FK_6C889BC19395C3F3 FOREIGN KEY (customer_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_category DROP CONSTRAINT FK_EBFD0C09727ACA70');
        $this->addSql('ALTER TABLE market_category ADD CONSTRAINT FK_EBFD0C09727ACA70 FOREIGN KEY (parent_id) REFERENCES market_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_customer_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_customer DROP CONSTRAINT FK_6C889BC19395C3F3');
        $this->addSql('DROP TABLE market_customer');
        $this->addSql('ALTER TABLE market_category DROP CONSTRAINT fk_ebfd0c09727aca70');
        $this->addSql('ALTER TABLE market_category ADD CONSTRAINT fk_ebfd0c09727aca70 FOREIGN KEY (parent_id) REFERENCES market_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }
}
