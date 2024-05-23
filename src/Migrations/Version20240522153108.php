<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240522153108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_message ADD parent_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_message ADD orders_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_message ADD CONSTRAINT FK_BF2BD8FF727ACA70 FOREIGN KEY (parent_id) REFERENCES market_message (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_message ADD CONSTRAINT FK_BF2BD8FFCFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_BF2BD8FF727ACA70 ON market_message (parent_id)');
        $this->addSql('CREATE INDEX IDX_BF2BD8FFCFFE9AD6 ON market_message (orders_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_message DROP CONSTRAINT FK_BF2BD8FF727ACA70');
        $this->addSql('ALTER TABLE market_message DROP CONSTRAINT FK_BF2BD8FFCFFE9AD6');
        $this->addSql('DROP INDEX IDX_BF2BD8FF727ACA70');
        $this->addSql('DROP INDEX IDX_BF2BD8FFCFFE9AD6');
        $this->addSql('ALTER TABLE market_message DROP parent_id');
        $this->addSql('ALTER TABLE market_message DROP orders_id');
    }
}
