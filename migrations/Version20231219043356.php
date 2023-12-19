<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231219043356 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market (id INT NOT NULL, owner_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, description TEXT DEFAULT NULL, slug VARCHAR(512) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAC85CB989D9B62 ON market (slug)');
        $this->addSql('CREATE INDEX IDX_6BAC85CB7E3C61F9 ON market (owner_id)');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product ADD market_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_product ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN market_product.created_at IS NULL');
        $this->addSql('ALTER TABLE market_product ADD CONSTRAINT FK_DADCEC2D622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_DADCEC2D622F3F37 ON market_product (market_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_product DROP CONSTRAINT FK_DADCEC2D622F3F37');
        $this->addSql('DROP SEQUENCE market_id_seq CASCADE');
        $this->addSql('ALTER TABLE market DROP CONSTRAINT FK_6BAC85CB7E3C61F9');
        $this->addSql('DROP TABLE market');
        $this->addSql('DROP INDEX IDX_DADCEC2D622F3F37');
        $this->addSql('ALTER TABLE market_product DROP market_id');
        $this->addSql('ALTER TABLE market_product ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE');
        $this->addSql('COMMENT ON COLUMN market_product.created_at IS \'(DC2Type:datetime_immutable)\'');
    }
}
