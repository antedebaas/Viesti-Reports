<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241013152649 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config DROP CONSTRAINT config_pkey');
        $this->addSql('ALTER TABLE config RENAME COLUMN key TO name');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX config_pkey');
        $this->addSql('ALTER TABLE config RENAME COLUMN name TO key');
        $this->addSql('ALTER TABLE config ADD PRIMARY KEY (key)');
    }
}
