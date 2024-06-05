<?php

declare(strict_types=1);

namespace DoctrineMigrations\SQLite;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302155243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smtptls_rdata_records (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_id INTEGER NOT NULL, usagetype INTEGER NOT NULL, selectortype INTEGER NOT NULL, matchingtype INTEGER NOT NULL, data CLOB NOT NULL, CONSTRAINT FK_65C68ADB2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_65C68ADB2D29E3C6 ON smtptls_rdata_records (policy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE smtptls_rdata_records');
    }
}
