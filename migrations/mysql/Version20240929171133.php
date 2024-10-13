<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240929171133 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE TABLE logs');
        $this->addSql('ALTER TABLE logs ADD state INT NOT NULL, ADD mailcount INT NOT NULL, DROP success, CHANGE message message LONGTEXT NOT NULL, CHANGE details details LONGTEXT NOT NULL');
        $this->addSql('ALTER TABLE users ADD first_name VARCHAR(255) DEFAULT NULL, ADD last_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, selector VARCHAR(20) NOT NULL, hashed_token VARCHAR(100) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, user_id INT NOT NULL, INDEX IDX_7CE748AA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('TRUNCATE TABLE config');
        $this->addSql('DROP INDEX `primary` ON config');
        $this->addSql('ALTER TABLE config ADD type VARCHAR(16) NOT NULL, CHANGE name `key` VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (`key`)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE TABLE logs');
        $this->addSql('ALTER TABLE logs ADD success TINYINT(1) NOT NULL, DROP state, DROP mailcount, CHANGE message message LONGTEXT DEFAULT NULL, CHANGE details details LONGTEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE users DROP first_name, DROP last_name');
        $this->addSql('ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395');
        $this->addSql('DROP TABLE reset_password_request');
        $this->addSql('TRUNCATE TABLE config');
        $this->addSql('DROP INDEX `PRIMARY` ON config');
        $this->addSql('ALTER TABLE config DROP type, CHANGE `key` name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (name)');
    }
}
