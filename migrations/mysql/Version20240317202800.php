<?php

declare(strict_types=1);

namespace DoctrineMigrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240317202800 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logs CHANGE success success TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE smtptls_failure_details CHANGE receiving_ip receiving_ip VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smtptls_failure_details CHANGE receiving_ip receiving_ip VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE logs CHANGE success success TINYINT(1) DEFAULT 1 NOT NULL');
    }
}
