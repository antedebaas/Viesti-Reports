<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005211613 extends AbstractMigration
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
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('TRUNCATE TABLE logs');
        $this->addSql('ALTER TABLE logs ADD success TINYINT(1) NOT NULL, DROP state, DROP mailcount, CHANGE message message LONGTEXT DEFAULT NULL, CHANGE details details LONGTEXT DEFAULT NULL');
    }
}
