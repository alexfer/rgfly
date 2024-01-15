<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240115060201 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE market_customer_session_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_product ADD short_title VARCHAR(80) NULL');
        $this->addSql('ALTER INDEX market_customer_sessions_sess_lifetime_idx RENAME TO sess_lifetime_idx');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SCHEMA web');
        $this->addSql('CREATE SEQUENCE market_customer_session_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('ALTER INDEX sess_lifetime_idx RENAME TO market_customer_sessions_sess_lifetime_idx');
        $this->addSql('ALTER TABLE market_product DROP short_title');
    }
}
