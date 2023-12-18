<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231218175129 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_category ALTER slug DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EBFD0C09989D9B62 ON market_category (slug)');
        $this->addSql('ALTER TABLE market_product ALTER slug DROP NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DADCEC2D989D9B62 ON market_product (slug)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP INDEX UNIQ_EBFD0C09989D9B62');
        $this->addSql('ALTER TABLE market_category ALTER slug SET NOT NULL');
        $this->addSql('DROP INDEX UNIQ_DADCEC2D989D9B62');
        $this->addSql('ALTER TABLE market_product ALTER slug SET NOT NULL');
    }
}
