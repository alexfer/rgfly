<?php

declare(strict_types=1);

namespace App\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240518135401 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id SERIAL NOT NULL, contact_id INT NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DADD4A25E7A1254A ON answer (contact_id)');
        $this->addSql('CREATE INDEX IDX_DADD4A25A76ED395 ON answer (user_id)');
        $this->addSql('CREATE TABLE attach (id SERIAL NOT NULL, user_details_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, size INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_DEC97C811C7DC1CE ON attach (user_details_id)');
        $this->addSql('CREATE TABLE category (id SERIAL NOT NULL, slug VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(512) DEFAULT NULL, position SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_64C19C1989D9B62 ON category (slug)');
        $this->addSql('CREATE TABLE contact (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, answers INT NOT NULL, phone VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, message TEXT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE entry (id SERIAL NOT NULL, user_id INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, comments INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2B219D70989D9B62 ON entry (slug)');
        $this->addSql('CREATE INDEX IDX_2B219D70A76ED395 ON entry (user_id)');
        $this->addSql('CREATE INDEX idx ON entry (status, type)');
        $this->addSql('CREATE TABLE entry_attachment (id SERIAL NOT NULL, attach_id INT DEFAULT NULL, details_id INT DEFAULT NULL, in_use INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E1F59089E784F8B7 ON entry_attachment (attach_id)');
        $this->addSql('CREATE INDEX IDX_E1F59089BB1A0722 ON entry_attachment (details_id)');
        $this->addSql('CREATE TABLE entry_category (id SERIAL NOT NULL, entry_id INT DEFAULT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_680BF989BA364942 ON entry_category (entry_id)');
        $this->addSql('CREATE INDEX IDX_680BF98912469DE2 ON entry_category (category_id)');
        $this->addSql('CREATE TABLE entry_details (id SERIAL NOT NULL, entry_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, short_content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5EC0A41DBA364942 ON entry_details (entry_id)');
        $this->addSql('CREATE TABLE faq (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, visible INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE market (id SERIAL NOT NULL, owner_id INT DEFAULT NULL, attach_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, address VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, slug VARCHAR(512) DEFAULT NULL, currency VARCHAR(5) DEFAULT NULL, website VARCHAR(1024) DEFAULT NULL, url VARCHAR(1024) DEFAULT NULL, description TEXT DEFAULT NULL, messages TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAC85CB989D9B62 ON market (slug)');
        $this->addSql('CREATE INDEX IDX_6BAC85CB7E3C61F9 ON market (owner_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6BAC85CBE784F8B7 ON market (attach_id)');
        $this->addSql('COMMENT ON COLUMN market.messages IS \'(DC2Type:simple_array)\'');
        $this->addSql('CREATE TABLE market_address (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, line1 VARCHAR(255) NOT NULL, line2 VARCHAR(255) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, country VARCHAR(5) NOT NULL, city VARCHAR(255) DEFAULT NULL, region VARCHAR(255) DEFAULT NULL, postal VARCHAR(50) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_4D887019395C3F3 ON market_address (customer_id)');
        $this->addSql('COMMENT ON COLUMN market_address.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_brand (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_D77C462D622F3F37 ON market_brand (market_id)');
        $this->addSql('CREATE TABLE market_category (id SERIAL NOT NULL, parent_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, description TEXT DEFAULT NULL, slug VARCHAR(512) DEFAULT NULL, level INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_EBFD0C09989D9B62 ON market_category (slug)');
        $this->addSql('CREATE INDEX IDX_EBFD0C09727ACA70 ON market_category (parent_id)');
        $this->addSql('COMMENT ON COLUMN market_category.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_category_product (id SERIAL NOT NULL, product_id INT DEFAULT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_89E568864584665A ON market_category_product (product_id)');
        $this->addSql('CREATE INDEX IDX_89E5688612469DE2 ON market_category_product (category_id)');
        $this->addSql('CREATE TABLE market_coupon (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, discount INT DEFAULT NULL, price NUMERIC(10, 2) DEFAULT NULL, event SMALLINT DEFAULT NULL, available INT NOT NULL, max_usage SMALLINT NOT NULL, type VARCHAR(50) NOT NULL, order_limit INT NOT NULL, promotion_text VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, started_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, expired_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_441B940E622F3F37 ON market_coupon (market_id)');
        $this->addSql('COMMENT ON COLUMN market_coupon.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_coupon.started_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_coupon.expired_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_coupon_market_product (market_coupon_id INT NOT NULL, market_product_id INT NOT NULL, PRIMARY KEY(market_coupon_id, market_product_id))');
        $this->addSql('CREATE INDEX IDX_771060B4F3976E8C ON market_coupon_market_product (market_coupon_id)');
        $this->addSql('CREATE INDEX IDX_771060B42B7A3246 ON market_coupon_market_product (market_product_id)');
        $this->addSql('CREATE TABLE market_coupon_code (id SERIAL NOT NULL, coupon_id INT DEFAULT NULL, code VARCHAR(50) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_23444E8166C5951B ON market_coupon_code (coupon_id)');
        $this->addSql('CREATE TABLE market_coupon_usage (id SERIAL NOT NULL, coupon_id INT DEFAULT NULL, customer_id INT DEFAULT NULL, coupon_code_id INT DEFAULT NULL, relation INT NOT NULL, used_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B4D4A7CE66C5951B ON market_coupon_usage (coupon_id)');
        $this->addSql('CREATE INDEX IDX_B4D4A7CE9395C3F3 ON market_coupon_usage (customer_id)');
        $this->addSql('CREATE INDEX IDX_B4D4A7CEC8B2BD81 ON market_coupon_usage (coupon_code_id)');
        $this->addSql('COMMENT ON COLUMN market_coupon_usage.used_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_customer (id SERIAL NOT NULL, member_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, phone VARCHAR(100) NOT NULL, country VARCHAR(5) NOT NULL, email VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6C889BC17597D3FE ON market_customer (member_id)');
        $this->addSql('COMMENT ON COLUMN market_customer.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_customer_message (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, market_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, message TEXT NOT NULL, phone VARCHAR(100) DEFAULT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_3717B8949395C3F3 ON market_customer_message (customer_id)');
        $this->addSql('CREATE INDEX IDX_3717B894622F3F37 ON market_customer_message (market_id)');
        $this->addSql('CREATE INDEX IDX_3717B894CFFE9AD6 ON market_customer_message (orders_id)');
        $this->addSql('COMMENT ON COLUMN market_customer_message.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN market_customer_message.updated_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_customer_orders (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, orders_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F1B1DB3A9395C3F3 ON market_customer_orders (customer_id)');
        $this->addSql('CREATE INDEX IDX_F1B1DB3ACFFE9AD6 ON market_customer_orders (orders_id)');
        $this->addSql('CREATE TABLE market_invoice (id SERIAL NOT NULL, orders_id INT DEFAULT NULL, payment_gateway_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, tax NUMERIC(10, 2) NOT NULL, amount NUMERIC(10, 2) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, paid_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_99F3FFC4CFFE9AD6 ON market_invoice (orders_id)');
        $this->addSql('CREATE INDEX IDX_99F3FFC462890FD5 ON market_invoice (payment_gateway_id)');
        $this->addSql('COMMENT ON COLUMN market_invoice.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_manufacturer (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B77B4092622F3F37 ON market_manufacturer (market_id)');
        $this->addSql('CREATE TABLE market_orders (id SERIAL NOT NULL, market_id INT DEFAULT NULL, number VARCHAR(50) NOT NULL, total NUMERIC(10, 2) DEFAULT NULL, session VARCHAR(255) DEFAULT NULL, status VARCHAR(100) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, completed_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C58B56E2622F3F37 ON market_orders (market_id)');
        $this->addSql('COMMENT ON COLUMN market_orders.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_orders_product (id SERIAL NOT NULL, orders_id INT DEFAULT NULL, product_id INT DEFAULT NULL, size VARCHAR(100) DEFAULT NULL, color VARCHAR(100) DEFAULT NULL, quantity INT NOT NULL, discount INT DEFAULT NULL, cost NUMERIC(10, 2) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_737098F1CFFE9AD6 ON market_orders_product (orders_id)');
        $this->addSql('CREATE INDEX IDX_737098F14584665A ON market_orders_product (product_id)');
        $this->addSql('CREATE TABLE market_payment_gateway (id SERIAL NOT NULL, name VARCHAR(100) NOT NULL, summary TEXT NOT NULL, active BOOLEAN NOT NULL, icon VARCHAR(50) NOT NULL, slug VARCHAR(255) NOT NULL, handler_text VARCHAR(100) NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE market_payment_gateway_market (id SERIAL NOT NULL, gateway_id INT DEFAULT NULL, market_id INT DEFAULT NULL, active BOOLEAN DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B019B14C577F8E00 ON market_payment_gateway_market (gateway_id)');
        $this->addSql('CREATE INDEX IDX_B019B14C622F3F37 ON market_payment_gateway_market (market_id)');
        $this->addSql('CREATE TABLE market_product (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(512) NOT NULL, description TEXT NOT NULL, slug VARCHAR(512) DEFAULT NULL, quantity INT NOT NULL, cost NUMERIC(10, 2) NOT NULL, short_name VARCHAR(80) NOT NULL, discount NUMERIC(10, 2) DEFAULT NULL, sku VARCHAR(255) DEFAULT NULL, pckg_quantity INT DEFAULT NULL, fee NUMERIC(10, 2) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_DADCEC2D989D9B62 ON market_product (slug)');
        $this->addSql('CREATE INDEX IDX_DADCEC2D622F3F37 ON market_product (market_id)');
        $this->addSql('CREATE TABLE market_product_attach (id SERIAL NOT NULL, product_id INT DEFAULT NULL, attach_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_EDFAD3024584665A ON market_product_attach (product_id)');
        $this->addSql('CREATE INDEX IDX_EDFAD302E784F8B7 ON market_product_attach (attach_id)');
        $this->addSql('CREATE TABLE market_product_attribute (id SERIAL NOT NULL, product_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, in_front INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_8F468B214584665A ON market_product_attribute (product_id)');
        $this->addSql('COMMENT ON COLUMN market_product_attribute.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE market_product_attribute_value (id SERIAL NOT NULL, attribute_id INT DEFAULT NULL, value VARCHAR(255) NOT NULL, extra JSON NOT NULL, in_use INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_C99FB8E4B6E62EFA ON market_product_attribute_value (attribute_id)');
        $this->addSql('CREATE TABLE market_product_brand (id SERIAL NOT NULL, product_id INT DEFAULT NULL, brand_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2CE09F354584665A ON market_product_brand (product_id)');
        $this->addSql('CREATE INDEX IDX_2CE09F3544F5D008 ON market_product_brand (brand_id)');
        $this->addSql('CREATE TABLE market_product_manufacturer (id SERIAL NOT NULL, product_id INT DEFAULT NULL, manufacturer_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_93EA846B4584665A ON market_product_manufacturer (product_id)');
        $this->addSql('CREATE INDEX IDX_93EA846BA23B42D ON market_product_manufacturer (manufacturer_id)');
        $this->addSql('CREATE TABLE market_product_supplier (id SERIAL NOT NULL, product_id INT DEFAULT NULL, supplier_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CDED2ABC4584665A ON market_product_supplier (product_id)');
        $this->addSql('CREATE INDEX IDX_CDED2ABC2ADD6D8C ON market_product_supplier (supplier_id)');
        $this->addSql('CREATE TABLE market_supplier (id SERIAL NOT NULL, market_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(3) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_769B79B6622F3F37 ON market_supplier (market_id)');
        $this->addSql('CREATE TABLE market_wishlist (id SERIAL NOT NULL, customer_id INT DEFAULT NULL, product_id INT DEFAULT NULL, market_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_71503FF99395C3F3 ON market_wishlist (customer_id)');
        $this->addSql('CREATE INDEX IDX_71503FF94584665A ON market_wishlist (product_id)');
        $this->addSql('CREATE INDEX IDX_71503FF9622F3F37 ON market_wishlist (market_id)');
        $this->addSql('COMMENT ON COLUMN market_wishlist.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE reset_password_request (id SERIAL NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE "user" (id SERIAL NOT NULL, attach_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E784F8B7 ON "user" (attach_id)');
        $this->addSql('CREATE TABLE user_details (id SERIAL NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, about TEXT DEFAULT NULL, date_birth TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_2A2B1580A76ED395 ON user_details (user_id)');
        $this->addSql('CREATE TABLE user_social (id SERIAL NOT NULL, details_id INT DEFAULT NULL, facebook_profile VARCHAR(512) DEFAULT NULL, twitter_profile VARCHAR(512) DEFAULT NULL, instagram_profile VARCHAR(512) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1433FABABB1A0722 ON user_social (details_id)');
        $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('COMMENT ON COLUMN messenger_messages.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.available_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN messenger_messages.delivered_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
            BEGIN
                PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                RETURN NEW;
            END;
        $$ LANGUAGE plpgsql;');
        $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
        $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
        $this->addSql('CREATE TABLE rememberme_token (series VARCHAR(88) NOT NULL, value VARCHAR(88) NOT NULL, lastUsed TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, class VARCHAR(100) NOT NULL, username VARCHAR(200) NOT NULL, PRIMARY KEY(series))');
        $this->addSql('COMMENT ON COLUMN rememberme_token.lastUsed IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE attach ADD CONSTRAINT FK_DEC97C811C7DC1CE FOREIGN KEY (user_details_id) REFERENCES user_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry ADD CONSTRAINT FK_2B219D70A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089E784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089BB1A0722 FOREIGN KEY (details_id) REFERENCES entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_category ADD CONSTRAINT FK_680BF989BA364942 FOREIGN KEY (entry_id) REFERENCES entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_category ADD CONSTRAINT FK_680BF98912469DE2 FOREIGN KEY (category_id) REFERENCES category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_details ADD CONSTRAINT FK_5EC0A41DBA364942 FOREIGN KEY (entry_id) REFERENCES entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CB7E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market ADD CONSTRAINT FK_6BAC85CBE784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_address ADD CONSTRAINT FK_4D887019395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_brand ADD CONSTRAINT FK_D77C462D622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_category ADD CONSTRAINT FK_EBFD0C09727ACA70 FOREIGN KEY (parent_id) REFERENCES market_category (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_category_product ADD CONSTRAINT FK_89E568864584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_category_product ADD CONSTRAINT FK_89E5688612469DE2 FOREIGN KEY (category_id) REFERENCES market_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon ADD CONSTRAINT FK_441B940E622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_market_product ADD CONSTRAINT FK_771060B4F3976E8C FOREIGN KEY (market_coupon_id) REFERENCES market_coupon (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_market_product ADD CONSTRAINT FK_771060B42B7A3246 FOREIGN KEY (market_product_id) REFERENCES market_product (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_code ADD CONSTRAINT FK_23444E8166C5951B FOREIGN KEY (coupon_id) REFERENCES market_coupon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CE66C5951B FOREIGN KEY (coupon_id) REFERENCES market_coupon (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CE9395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_coupon_usage ADD CONSTRAINT FK_B4D4A7CEC8B2BD81 FOREIGN KEY (coupon_code_id) REFERENCES market_coupon_code (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer ADD CONSTRAINT FK_6C889BC17597D3FE FOREIGN KEY (member_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B8949395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B894622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_message ADD CONSTRAINT FK_3717B894CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_orders ADD CONSTRAINT FK_F1B1DB3A9395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_customer_orders ADD CONSTRAINT FK_F1B1DB3ACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC4CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_invoice ADD CONSTRAINT FK_99F3FFC462890FD5 FOREIGN KEY (payment_gateway_id) REFERENCES market_payment_gateway (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_manufacturer ADD CONSTRAINT FK_B77B4092622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_orders ADD CONSTRAINT FK_C58B56E2622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_orders_product ADD CONSTRAINT FK_737098F1CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES market_orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_orders_product ADD CONSTRAINT FK_737098F14584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_payment_gateway_market ADD CONSTRAINT FK_B019B14C577F8E00 FOREIGN KEY (gateway_id) REFERENCES market_payment_gateway (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_payment_gateway_market ADD CONSTRAINT FK_B019B14C622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product ADD CONSTRAINT FK_DADCEC2D622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attach ADD CONSTRAINT FK_EDFAD3024584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attach ADD CONSTRAINT FK_EDFAD302E784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attribute ADD CONSTRAINT FK_8F468B214584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_attribute_value ADD CONSTRAINT FK_C99FB8E4B6E62EFA FOREIGN KEY (attribute_id) REFERENCES market_product_attribute (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT FK_2CE09F354584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_brand ADD CONSTRAINT FK_2CE09F3544F5D008 FOREIGN KEY (brand_id) REFERENCES market_brand (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_manufacturer ADD CONSTRAINT FK_93EA846B4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_manufacturer ADD CONSTRAINT FK_93EA846BA23B42D FOREIGN KEY (manufacturer_id) REFERENCES market_manufacturer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_supplier ADD CONSTRAINT FK_CDED2ABC4584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_product_supplier ADD CONSTRAINT FK_CDED2ABC2ADD6D8C FOREIGN KEY (supplier_id) REFERENCES market_supplier (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_supplier ADD CONSTRAINT FK_769B79B6622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF99395C3F3 FOREIGN KEY (customer_id) REFERENCES market_customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF94584665A FOREIGN KEY (product_id) REFERENCES market_product (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE market_wishlist ADD CONSTRAINT FK_71503FF9622F3F37 FOREIGN KEY (market_id) REFERENCES market (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "user" ADD CONSTRAINT FK_8D93D649E784F8B7 FOREIGN KEY (attach_id) REFERENCES attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_details ADD CONSTRAINT FK_2A2B1580A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE user_social ADD CONSTRAINT FK_1433FABABB1A0722 FOREIGN KEY (details_id) REFERENCES user_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A25E7A1254A');
        $this->addSql('ALTER TABLE answer DROP CONSTRAINT FK_DADD4A25A76ED395');
        $this->addSql('ALTER TABLE attach DROP CONSTRAINT FK_DEC97C811C7DC1CE');
        $this->addSql('ALTER TABLE entry DROP CONSTRAINT FK_2B219D70A76ED395');
        $this->addSql('ALTER TABLE entry_attachment DROP CONSTRAINT FK_E1F59089E784F8B7');
        $this->addSql('ALTER TABLE entry_attachment DROP CONSTRAINT FK_E1F59089BB1A0722');
        $this->addSql('ALTER TABLE entry_category DROP CONSTRAINT FK_680BF989BA364942');
        $this->addSql('ALTER TABLE entry_category DROP CONSTRAINT FK_680BF98912469DE2');
        $this->addSql('ALTER TABLE entry_details DROP CONSTRAINT FK_5EC0A41DBA364942');
        $this->addSql('ALTER TABLE market DROP CONSTRAINT FK_6BAC85CB7E3C61F9');
        $this->addSql('ALTER TABLE market DROP CONSTRAINT FK_6BAC85CBE784F8B7');
        $this->addSql('ALTER TABLE market_address DROP CONSTRAINT FK_4D887019395C3F3');
        $this->addSql('ALTER TABLE market_brand DROP CONSTRAINT FK_D77C462D622F3F37');
        $this->addSql('ALTER TABLE market_category DROP CONSTRAINT FK_EBFD0C09727ACA70');
        $this->addSql('ALTER TABLE market_category_product DROP CONSTRAINT FK_89E568864584665A');
        $this->addSql('ALTER TABLE market_category_product DROP CONSTRAINT FK_89E5688612469DE2');
        $this->addSql('ALTER TABLE market_coupon DROP CONSTRAINT FK_441B940E622F3F37');
        $this->addSql('ALTER TABLE market_coupon_market_product DROP CONSTRAINT FK_771060B4F3976E8C');
        $this->addSql('ALTER TABLE market_coupon_market_product DROP CONSTRAINT FK_771060B42B7A3246');
        $this->addSql('ALTER TABLE market_coupon_code DROP CONSTRAINT FK_23444E8166C5951B');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CE66C5951B');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CE9395C3F3');
        $this->addSql('ALTER TABLE market_coupon_usage DROP CONSTRAINT FK_B4D4A7CEC8B2BD81');
        $this->addSql('ALTER TABLE market_customer DROP CONSTRAINT FK_6C889BC17597D3FE');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B8949395C3F3');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B894622F3F37');
        $this->addSql('ALTER TABLE market_customer_message DROP CONSTRAINT FK_3717B894CFFE9AD6');
        $this->addSql('ALTER TABLE market_customer_orders DROP CONSTRAINT FK_F1B1DB3A9395C3F3');
        $this->addSql('ALTER TABLE market_customer_orders DROP CONSTRAINT FK_F1B1DB3ACFFE9AD6');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC4CFFE9AD6');
        $this->addSql('ALTER TABLE market_invoice DROP CONSTRAINT FK_99F3FFC462890FD5');
        $this->addSql('ALTER TABLE market_manufacturer DROP CONSTRAINT FK_B77B4092622F3F37');
        $this->addSql('ALTER TABLE market_orders DROP CONSTRAINT FK_C58B56E2622F3F37');
        $this->addSql('ALTER TABLE market_orders_product DROP CONSTRAINT FK_737098F1CFFE9AD6');
        $this->addSql('ALTER TABLE market_orders_product DROP CONSTRAINT FK_737098F14584665A');
        $this->addSql('ALTER TABLE market_payment_gateway_market DROP CONSTRAINT FK_B019B14C577F8E00');
        $this->addSql('ALTER TABLE market_payment_gateway_market DROP CONSTRAINT FK_B019B14C622F3F37');
        $this->addSql('ALTER TABLE market_product DROP CONSTRAINT FK_DADCEC2D622F3F37');
        $this->addSql('ALTER TABLE market_product_attach DROP CONSTRAINT FK_EDFAD3024584665A');
        $this->addSql('ALTER TABLE market_product_attach DROP CONSTRAINT FK_EDFAD302E784F8B7');
        $this->addSql('ALTER TABLE market_product_attribute DROP CONSTRAINT FK_8F468B214584665A');
        $this->addSql('ALTER TABLE market_product_attribute_value DROP CONSTRAINT FK_C99FB8E4B6E62EFA');
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT FK_2CE09F354584665A');
        $this->addSql('ALTER TABLE market_product_brand DROP CONSTRAINT FK_2CE09F3544F5D008');
        $this->addSql('ALTER TABLE market_product_manufacturer DROP CONSTRAINT FK_93EA846B4584665A');
        $this->addSql('ALTER TABLE market_product_manufacturer DROP CONSTRAINT FK_93EA846BA23B42D');
        $this->addSql('ALTER TABLE market_product_supplier DROP CONSTRAINT FK_CDED2ABC4584665A');
        $this->addSql('ALTER TABLE market_product_supplier DROP CONSTRAINT FK_CDED2ABC2ADD6D8C');
        $this->addSql('ALTER TABLE market_supplier DROP CONSTRAINT FK_769B79B6622F3F37');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF99395C3F3');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF94584665A');
        $this->addSql('ALTER TABLE market_wishlist DROP CONSTRAINT FK_71503FF9622F3F37');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE "user" DROP CONSTRAINT FK_8D93D649E784F8B7');
        $this->addSql('ALTER TABLE user_details DROP CONSTRAINT FK_2A2B1580A76ED395');
        $this->addSql('ALTER TABLE user_social DROP CONSTRAINT FK_1433FABABB1A0722');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE attach');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE contact');
        $this->addSql('DROP TABLE entry');
        $this->addSql('DROP TABLE entry_attachment');
        $this->addSql('DROP TABLE entry_category');
        $this->addSql('DROP TABLE entry_details');
        $this->addSql('DROP TABLE faq');
        $this->addSql('DROP TABLE market');
        $this->addSql('DROP TABLE market_address');
        $this->addSql('DROP TABLE market_brand');
        $this->addSql('DROP TABLE market_category');
        $this->addSql('DROP TABLE market_category_product');
        $this->addSql('DROP TABLE market_coupon');
        $this->addSql('DROP TABLE market_coupon_market_product');
        $this->addSql('DROP TABLE market_coupon_code');
        $this->addSql('DROP TABLE market_coupon_usage');
        $this->addSql('DROP TABLE market_customer');
        $this->addSql('DROP TABLE market_customer_message');
        $this->addSql('DROP TABLE market_customer_orders');
        $this->addSql('DROP TABLE market_invoice');
        $this->addSql('DROP TABLE market_manufacturer');
        $this->addSql('DROP TABLE market_orders');
        $this->addSql('DROP TABLE market_orders_product');
        $this->addSql('DROP TABLE market_payment_gateway');
        $this->addSql('DROP TABLE market_payment_gateway_market');
        $this->addSql('DROP TABLE market_product');
        $this->addSql('DROP TABLE market_product_attach');
        $this->addSql('DROP TABLE market_product_attribute');
        $this->addSql('DROP TABLE market_product_attribute_value');
        $this->addSql('DROP TABLE market_product_brand');
        $this->addSql('DROP TABLE market_product_manufacturer');
        $this->addSql('DROP TABLE market_product_supplier');
        $this->addSql('DROP TABLE market_supplier');
        $this->addSql('DROP TABLE market_wishlist');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE "user"');
        $this->addSql('DROP TABLE user_details');
        $this->addSql('DROP TABLE user_social');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}
