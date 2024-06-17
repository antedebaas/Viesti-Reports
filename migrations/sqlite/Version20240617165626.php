<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240617165626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config (name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('ALTER TABLE logs ADD COLUMN details CLOB DEFAULT NULL');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, email, roles, password, is_verified FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO users (id, email, roles, password, is_verified) SELECT id, email, roles, password, is_verified FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE config');
        $this->addSql('CREATE TEMPORARY TABLE __temp__logs AS SELECT id, time, message, success FROM logs');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL, success BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO logs (id, time, message, success) SELECT id, time, message, success FROM __temp__logs');
        $this->addSql('DROP TABLE __temp__logs');
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, email, roles, password, is_verified FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO users (id, email, roles, password, is_verified) SELECT id, email, roles, password, is_verified FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
    }
}
