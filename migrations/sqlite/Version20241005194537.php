<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005194537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB NOT NULL, details CLOB NOT NULL, state INTEGER NOT NULL, mailcount INTEGER NOT NULL)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL, details CLOB DEFAULT NULL, success BOOLEAN NOT NULL)');
    }
}
