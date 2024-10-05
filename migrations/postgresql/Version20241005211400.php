<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241005211400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logs ADD state INT NOT NULL');
        $this->addSql('ALTER TABLE logs ADD mailcount INT NOT NULL');
        $this->addSql('ALTER TABLE logs DROP success');
        $this->addSql('ALTER TABLE logs ALTER message SET NOT NULL');
        $this->addSql('ALTER TABLE logs ALTER details SET NOT NULL');
        $this->addSql('TRUNCATE logs');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE logs ADD success BOOLEAN NOT NULL');
        $this->addSql('ALTER TABLE logs DROP state');
        $this->addSql('ALTER TABLE logs DROP mailcount');
        $this->addSql('ALTER TABLE logs ALTER message DROP NOT NULL');
        $this->addSql('ALTER TABLE logs ALTER details DROP NOT NULL');
        $this->addSql('TRUNCATE logs');
    }
}
