<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231201165955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE public.answer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.attach_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.contact_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.entry_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE entry_attachment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.entry_category_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.entry_details_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.faq_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE reset_password_request_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE public.user_details_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE public.answer (id INT NOT NULL, contact_id INT NOT NULL, user_id INT NOT NULL, message TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_90333E05E7A1254A ON public.answer (contact_id)');
        $this->addSql('CREATE INDEX IDX_90333E05A76ED395 ON public.answer (user_id)');
        $this->addSql('CREATE TABLE public.attach (id INT NOT NULL, user_details_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, mime VARCHAR(255) NOT NULL, size INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_942708A11C7DC1CE ON public.attach (user_details_id)');
        $this->addSql('CREATE TABLE public.category (id INT NOT NULL, slug VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(512) DEFAULT NULL, position SMALLINT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C4A0C2A8989D9B62 ON public.category (slug)');
        $this->addSql('CREATE TABLE public.contact (id INT NOT NULL, name VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, answers INT NOT NULL, phone VARCHAR(255) DEFAULT NULL, subject VARCHAR(255) DEFAULT NULL, message TEXT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE public.entry (id INT NOT NULL, user_id INT DEFAULT NULL, slug VARCHAR(255) DEFAULT NULL, type VARCHAR(255) NOT NULL, status VARCHAR(255) NOT NULL, comments INT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1AF0FC14989D9B62 ON public.entry (slug)');
        $this->addSql('CREATE INDEX IDX_1AF0FC14A76ED395 ON public.entry (user_id)');
        $this->addSql('CREATE INDEX idx ON public.entry (status, type)');
        $this->addSql('CREATE TABLE entry_attachment (id INT NOT NULL, attach_id INT DEFAULT NULL, details_id INT DEFAULT NULL, in_use INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_E1F59089E784F8B7 ON entry_attachment (attach_id)');
        $this->addSql('CREATE INDEX IDX_E1F59089BB1A0722 ON entry_attachment (details_id)');
        $this->addSql('CREATE TABLE public.entry_category (id INT NOT NULL, entry_id INT DEFAULT NULL, category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B75755FDBA364942 ON public.entry_category (entry_id)');
        $this->addSql('CREATE INDEX IDX_B75755FD12469DE2 ON public.entry_category (category_id)');
        $this->addSql('CREATE TABLE public.entry_details (id INT NOT NULL, entry_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, content TEXT DEFAULT NULL, short_content TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_628313B1BA364942 ON public.entry_details (entry_id)');
        $this->addSql('CREATE INDEX IDX_628313B1FEC530A9 ON public.entry_details (content)');
        $this->addSql('CREATE TABLE public.faq (id INT NOT NULL, title VARCHAR(255) NOT NULL, content TEXT NOT NULL, visible INT DEFAULT 0 NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE reset_password_request (id INT NOT NULL, user_id INT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, expires_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('COMMENT ON COLUMN reset_password_request.requested_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN reset_password_request.expires_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('CREATE TABLE public."user" (id INT NOT NULL, attach_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, ip VARCHAR(255) DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, deleted_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E7927C74 ON public."user" (email)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_327C5DE7E784F8B7 ON public."user" (attach_id)');
        $this->addSql('CREATE TABLE public.user_details (id INT NOT NULL, user_id INT DEFAULT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, country VARCHAR(2) DEFAULT NULL, city VARCHAR(255) DEFAULT NULL, about TEXT DEFAULT NULL, date_birth TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_6A7868A4A76ED395 ON public.user_details (user_id)');
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
        $this->addSql('ALTER TABLE public.answer ADD CONSTRAINT FK_90333E05E7A1254A FOREIGN KEY (contact_id) REFERENCES public.contact (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.answer ADD CONSTRAINT FK_90333E05A76ED395 FOREIGN KEY (user_id) REFERENCES public."user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.attach ADD CONSTRAINT FK_942708A11C7DC1CE FOREIGN KEY (user_details_id) REFERENCES public.user_details (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.entry ADD CONSTRAINT FK_1AF0FC14A76ED395 FOREIGN KEY (user_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089E784F8B7 FOREIGN KEY (attach_id) REFERENCES public.attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE entry_attachment ADD CONSTRAINT FK_E1F59089BB1A0722 FOREIGN KEY (details_id) REFERENCES public.entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.entry_category ADD CONSTRAINT FK_B75755FDBA364942 FOREIGN KEY (entry_id) REFERENCES public.entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.entry_category ADD CONSTRAINT FK_B75755FD12469DE2 FOREIGN KEY (category_id) REFERENCES public.category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.entry_details ADD CONSTRAINT FK_628313B1BA364942 FOREIGN KEY (entry_id) REFERENCES public.entry (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public."user" ADD CONSTRAINT FK_327C5DE7E784F8B7 FOREIGN KEY (attach_id) REFERENCES public.attach (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE public.user_details ADD CONSTRAINT FK_6A7868A4A76ED395 FOREIGN KEY (user_id) REFERENCES public."user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE public.answer_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.attach_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.contact_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.entry_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE entry_attachment_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.entry_category_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.entry_details_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.faq_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE reset_password_request_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.user_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE public.user_details_id_seq CASCADE');
        $this->addSql('ALTER TABLE public.answer DROP CONSTRAINT FK_90333E05E7A1254A');
        $this->addSql('ALTER TABLE public.answer DROP CONSTRAINT FK_90333E05A76ED395');
        $this->addSql('ALTER TABLE public.attach DROP CONSTRAINT FK_942708A11C7DC1CE');
        $this->addSql('ALTER TABLE public.entry DROP CONSTRAINT FK_1AF0FC14A76ED395');
        $this->addSql('ALTER TABLE entry_attachment DROP CONSTRAINT FK_E1F59089E784F8B7');
        $this->addSql('ALTER TABLE entry_attachment DROP CONSTRAINT FK_E1F59089BB1A0722');
        $this->addSql('ALTER TABLE public.entry_category DROP CONSTRAINT FK_B75755FDBA364942');
        $this->addSql('ALTER TABLE public.entry_category DROP CONSTRAINT FK_B75755FD12469DE2');
        $this->addSql('ALTER TABLE public.entry_details DROP CONSTRAINT FK_628313B1BA364942');
        $this->addSql('ALTER TABLE reset_password_request DROP CONSTRAINT FK_7CE748AA76ED395');
        $this->addSql('ALTER TABLE public."user" DROP CONSTRAINT FK_327C5DE7E784F8B7');
        $this->addSql('ALTER TABLE public.user_details DROP CONSTRAINT FK_6A7868A4A76ED395');
        $this->addSql('DROP TABLE public.answer');
        $this->addSql('DROP TABLE public.attach');
        $this->addSql('DROP TABLE public.category');
        $this->addSql('DROP TABLE public.contact');
        $this->addSql('DROP TABLE public.entry');
        $this->addSql('DROP TABLE entry_attachment');
        $this->addSql('DROP TABLE public.entry_category');
        $this->addSql('DROP TABLE public.entry_details');
        $this->addSql('DROP TABLE public.faq');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE public."user"');
        $this->addSql('DROP TABLE public.user_details');
        $this->addSql('DROP TABLE messenger_messages');
        $this->addSql('DROP TABLE rememberme_token');
    }
}
