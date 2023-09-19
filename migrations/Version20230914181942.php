<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230914181942 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('RENAME TABLE records TO dmarc_records');
        $this->addSql('RENAME TABLE reports TO dmarc_reports');
        $this->addSql('RENAME TABLE results TO dmarc_results');
        $this->addSql('RENAME TABLE seen TO dmarc_seen');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('RENAME TABLE dmarc_records TO records');
        $this->addSql('RENAME TABLE dmarc_reports TO reports');
        $this->addSql('RENAME TABLE dmarc_results TO results');
        $this->addSql('RENAME TABLE dmarc_seen TO seen');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
