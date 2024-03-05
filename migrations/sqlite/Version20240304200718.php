<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240304200718 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logs ADD COLUMN success BOOLEAN NOT NULL default true');
        $this->addSql('UPDATE logs SET success = true');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__logs AS SELECT id, time, message FROM logs');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO logs (id, time, message) SELECT id, time, message FROM __temp__logs');
        $this->addSql('DROP TABLE __temp__logs');
    }
}
