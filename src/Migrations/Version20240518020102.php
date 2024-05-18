<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518020102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE market_coupon_usage ADD coupon_code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CEC8B2BD81 FOREIGN KEY (coupon_code_id) REFERENCES market_coupon_code (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B4D4A7CEC8B2BD81 ON market_coupon_usage (coupon_code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CEC8B2BD81');
        $this->addSql('DROP INDEX IDX_B4D4A7CEC8B2BD81');
        $this->addSql('ALTER TABLE market_coupon_usage DROP coupon_code_id');
    }
}
