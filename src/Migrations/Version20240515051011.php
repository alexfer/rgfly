<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515051011 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_coupon_code_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_coupon_code (id INT NOT NULL, coupon_id INT DEFAULT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23444E8166C5951B ON market_coupon_code (coupon_id)');
        $this->addSql('ALTER TABLE market_coupon_code ADD CONSTRAINT FK_23444E8166C5951B FOREIGN KEY (coupon_id) REFERENCES market_coupon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon DROP code');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_coupon_code_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_coupon_code DROP CONSTRAINT FK_23444E8166C5951B');
        $this->addSql('DROP TABLE market_coupon_code');
        $this->addSql('ALTER TABLE market_coupon ADD code VARCHAR(255) NOT NULL');
    }
}
