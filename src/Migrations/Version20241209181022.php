<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241209181022 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store_payment_gateway ADD attach_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE store_payment_gateway ADD CONSTRAINT FK_6CCBBA5BE784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6CCBBA5BE784F8B7 ON store_payment_gateway (attach_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store_payment_gateway DROP CONSTRAINT FK_6CCBBA5BE784F8B7');
        $this->addSql('DROP INDEX UNIQ_6CCBBA5BE784F8B7');
        $this->addSql('ALTER TABLE store_payment_gateway DROP attach_id');
    }
}
