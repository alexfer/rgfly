<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231217204632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE market_category_product_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE market_category_product (id INT NOT NULL, product_id INT DEFAULT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89E568864584665A ON market_category_product (product_id)');
        $this->addSql('CREATE INDEX IDX_89E5688612469DE2 ON market_category_product (category_id)');
        $this->addSql('ALTER TABLE market_category_product ADD CONSTRAINT FK_89E568864584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_category_product ADD CONSTRAINT FK_89E5688612469DE2 FOREIGN KEY (category_id) REFERENCES market_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE market_category_product_id_seq CASCADE');
        $this->addSql('ALTER TABLE market_category_product DROP CONSTRAINT FK_89E568864584665A');
        $this->addSql('ALTER TABLE market_category_product DROP CONSTRAINT FK_89E5688612469DE2');
        $this->addSql('DROP TABLE market_category_product');
    }
}
