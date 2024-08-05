<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240805031709 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store ALTER country DROP DEFAULT');
        $this->addSql('ALTER TABLE store ALTER country SET NOT NULL');
        $this->addSql('ALTER TABLE store_message ADD identity UUID NOT NULL');
        $this->addSql('COMMENT ON COLUMN store_message.identity IS \'(DC2Type:uuid)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store ALTER country SET DEFAULT \'(NOT NULL::boolean)\'');
        $this->addSql('ALTER TABLE store ALTER country DROP NOT NULL');
        $this->addSql('ALTER TABLE store_message DROP identity');
    }
}
