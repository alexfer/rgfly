<?php

declare(strict_types=1);

namespace AppMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240915041157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE store_carrier (id SERIAL NOT NULL, attach_id INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, link_url VARCHAR(255) DEFAULT NULL, description VARCHAR(255) DEFAULT NULL, shipping_amount INT DEFAULT NULL, is_enabled BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5E31D0FBE784F8B7 ON store_carrier (attach_id)');
        $this->addSql('CREATE TABLE store_shipment (id SERIAL NOT NULL, orders_id INT DEFAULT NULL, carrier_id INT DEFAULT NULL, coupon_id INT DEFAULT NULL, tracking_number VARCHAR(255) DEFAULT NULL, tracking_number_url VARCHAR(512) DEFAULT NULL, shipped_at VARCHAR(255) DEFAULT NULL, received_at DATE DEFAULT NULL, returned_at DATE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3CBC5F26CFFE9AD6 ON store_shipment (orders_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3CBC5F2621DFC797 ON store_shipment (carrier_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_3CBC5F2666C5951B ON store_shipment (coupon_id)');
        $this->addSql('ALTER TABLE store_carrier ADD CONSTRAINT FK_5E31D0FBE784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store_shipment ADD CONSTRAINT FK_3CBC5F26CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES store_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store_shipment ADD CONSTRAINT FK_3CBC5F2621DFC797 FOREIGN KEY (carrier_id) REFERENCES store_carrier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE store_shipment ADD CONSTRAINT FK_3CBC5F2666C5951B FOREIGN KEY (coupon_id) REFERENCES store_coupon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE store_carrier DROP CONSTRAINT FK_5E31D0FBE784F8B7');
        $this->addSql('ALTER TABLE store_shipment DROP CONSTRAINT FK_3CBC5F26CFFE9AD6');
        $this->addSql('ALTER TABLE store_shipment DROP CONSTRAINT FK_3CBC5F2621DFC797');
        $this->addSql('ALTER TABLE store_shipment DROP CONSTRAINT FK_3CBC5F2666C5951B');
        $this->addSql('DROP TABLE store_carrier');
        $this->addSql('DROP TABLE store_shipment');
    }
}
