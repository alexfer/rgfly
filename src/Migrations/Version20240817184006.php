<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240817184006 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE store_operation (id SERIAL NOT NULL, store_id INT DEFAULT NULL, format VARCHAR(255) NOT NULL, revision VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_44D7CA10B092A811 ON store_operation (store_id)');
        $this->addSql('COMMENT ON COLUMN store_operation.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE store_operation ADD CONSTRAINT FK_44D7CA10B092A811 FOREIGN KEY (store_id) REFERENCES store (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store_operation DROP CONSTRAINT FK_44D7CA10B092A811');
        $this->addSql('DROP TABLE store_operation');
    }
}
