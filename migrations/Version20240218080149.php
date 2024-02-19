<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240218080149 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_payment_gateway_market_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_payment_gateway_market (id INT NOT NULL, gateway_id INT DEFAULT NULL, market_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B019B14C577F8E00 ON market_payment_gateway_market (gateway_id)');
        $this->addSql('CREATE INDEX IDX_B019B14C622F3F37 ON market_payment_gateway_market (market_id)');
        $this->addSql('ALTER TABLE market_payment_gateway_market ADD CONSTRAINT FK_B019B14C577F8E00 FOREIGN KEY (gateway_id) REFERENCES market_payment_gateway (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_payment_gateway_market ADD CONSTRAINT FK_B019B14C622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_payment_gateway_market_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_payment_gateway_market DROP CONSTRAINT FK_B019B14C577F8E00');
        $this->addSql('ALTER TABLE market_payment_gateway_market DROP CONSTRAINT FK_B019B14C622F3F37');
        $this->addSql('DROP TABLE market_payment_gateway_market');
    }
}
