<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240928132956 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD COLUMN first_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE users ADD COLUMN last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE TABLE reset_password_request (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB NOT NULL, details CLOB NOT NULL, state INTEGER NOT NULL, mailcount INTEGER NOT NULL)');
        $this->addSql('DROP TABLE config');
        $this->addSql('CREATE TABLE config ("key" VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY("key"))');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__users AS SELECT id, email, roles, password, is_verified FROM users');
        $this->addSql('DROP TABLE users');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO users (id, email, roles, password, is_verified) SELECT id, email, roles, password, is_verified FROM __temp__users');
        $this->addSql('DROP TABLE __temp__users');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL, details CLOB DEFAULT NULL, success BOOLEAN NOT NULL)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__config AS SELECT "key", value FROM config');
        $this->addSql('DROP TABLE config');
        $this->addSql('CREATE TABLE config (name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('INSERT INTO config (name, value) SELECT "key", value FROM __temp__config');
        $this->addSql('DROP TABLE __temp__config');
    }
}
