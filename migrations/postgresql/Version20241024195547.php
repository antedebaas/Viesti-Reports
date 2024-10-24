<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024195547 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM "config" WHERE "name" = \'getreportsfrommailbox.lock\';');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'check_mailbox_lock\', \'0\', \'boolean\') ON CONFLICT ("name") DO NOTHING;');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'delete_processed_mails\', \'0\', \'boolean\') ON CONFLICT ("name") DO NOTHING;');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'enable_registration\', \'0\', \'boolean\') ON CONFLICT ("name") DO NOTHING;');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'enable_pushover\', \'0\', \'boolean\') ON CONFLICT ("name") DO NOTHING;');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'pushover_user_key\', \'\', \'string\') ON CONFLICT ("name") DO NOTHING;');
        $this->addSql('INSERT INTO "config" ("name", "value", "type") VALUES (\'pushover_api_key\', \'\', \'string\') ON CONFLICT ("name") DO NOTHING;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM "config" WHERE "name" = \'pushover_user_key\';');
        $this->addSql('DELETE FROM "config" WHERE "name" = \'pushover_api_key\';');
        $this->addSql('DELETE FROM "config" WHERE "name" = \'enable_registration\';');
        $this->addSql('DELETE FROM "config" WHERE "name" = \'enable_pushover\';');
        $this->addSql('DELETE FROM "config" WHERE "name" = \'delete_processed_mails\';');
        $this->addSql('DELETE FROM "config" WHERE "name" = \'check_mailbox_lock\';');
    }
}
