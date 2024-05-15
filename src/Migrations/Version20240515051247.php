<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240515051247 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_coupon_code_id_seq');
        $this->addSql('SELECT setval(\'market_coupon_code_id_seq\', (SELECT MAX(id) FROM market_coupon_code))');
        $this->addSql('ALTER TABLE market_coupon_code ALTER id SET DEFAULT nextval(\'market_coupon_code_id_seq\')');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_coupon_code ALTER id DROP DEFAULT');
    }
}
