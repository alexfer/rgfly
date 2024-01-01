<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240101070923 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_invoice ADD payment_method_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_invoice DROP payment_method');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC45AA1164F FOREIGN KEY (payment_method_id) REFERENCES market_payment_method (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99F3FFC45AA1164F ON market_invoice (payment_method_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC45AA1164F');
        $this->addSql('DROP INDEX UNIQ_99F3FFC45AA1164F');
        $this->addSql('ALTER TABLE market_invoice ADD payment_method VARCHAR(100) DEFAULT NULL');
        $this->addSql('ALTER TABLE market_invoice DROP payment_method_id');
    }
}
