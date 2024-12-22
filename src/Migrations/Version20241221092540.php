<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241221092540 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX uniq_896d36a321dfc797');
        $this->addSql('CREATE INDEX IDX_896D36A321DFC797 ON store_invoice (carrier_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_896D36A321DFC797');
        $this->addSql('CREATE UNIQUE INDEX uniq_896d36a321dfc797 ON store_invoice (carrier_id)');
    }
}
