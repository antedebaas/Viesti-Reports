<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013152748 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__config AS SELECT "key", value, type FROM config');
        $this->addSql('DROP TABLE config');
        $this->addSql('CREATE TABLE config (name VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY(name))');
        $this->addSql('INSERT INTO config (name, value, type) SELECT "key", value, type FROM __temp__config');
        $this->addSql('DROP TABLE __temp__config');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__config AS SELECT name, value, type FROM config');
        $this->addSql('DROP TABLE config');
        $this->addSql('CREATE TABLE config ("key" VARCHAR(255) NOT NULL, value VARCHAR(255) NOT NULL, type VARCHAR(16) NOT NULL, PRIMARY KEY("key"))');
        $this->addSql('INSERT INTO config ("key", value, type) SELECT name, value, type FROM __temp__config');
        $this->addSql('DROP TABLE __temp__config');
    }
}
