<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304165303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_invoice ADD payment_gateway_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC462890FD5 FOREIGN KEY (payment_gateway_id) REFERENCES market_payment_gateway (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_99F3FFC462890FD5 ON market_invoice (payment_gateway_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC462890FD5');
        $this->addSql('DROP INDEX IDX_99F3FFC462890FD5');
        $this->addSql('ALTER TABLE market_invoice DROP payment_gateway_id');
    }
}
