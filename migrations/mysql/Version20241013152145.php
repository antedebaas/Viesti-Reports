<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013152145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `primary` ON config');
        $this->addSql('ALTER TABLE config CHANGE `key` name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX `PRIMARY` ON config');
        $this->addSql('ALTER TABLE config CHANGE name `key` VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (`key`)');
    }
}
