<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918185015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('RENAME TABLE seen TO dmarc_seen');
        $this->addSql('CREATE TABLE mtasts_seen (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_CC664AEC4BD2A4C0 (report_id), INDEX IDX_CC664AECA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mtasts_seen ADD CONSTRAINT FK_CC664AEC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES mtasts_reports (id)');
        $this->addSql('ALTER TABLE mtasts_seen ADD CONSTRAINT FK_CC664AECA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mtasts_seen DROP FOREIGN KEY FK_CC664AEC4BD2A4C0');
        $this->addSql('ALTER TABLE mtasts_seen DROP FOREIGN KEY FK_CC664AECA76ED395');
        $this->addSql('RENAME TABLE dmarc_seen TO seen');
    }
}
