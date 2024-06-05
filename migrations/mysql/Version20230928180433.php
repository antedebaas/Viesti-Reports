<?php

declare(strict_types=1);

namespace DoctrineMigrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230928180433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function isTransactional(): bool
    {
        return false;
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dmarc_records DROP FOREIGN KEY FK_FC3C5CC24BD2A4C0');
        $this->addSql('DROP INDEX idx_9c9d58464bd2a4c0 ON dmarc_records');
        $this->addSql('CREATE INDEX IDX_FC3C5CC24BD2A4C0 ON dmarc_records (report_id)');
        $this->addSql('ALTER TABLE dmarc_records ADD CONSTRAINT FK_FC3C5CC24BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_reports DROP FOREIGN KEY FK_91BEA3C1115F0EE5');
        $this->addSql('DROP INDEX idx_f11fa745115f0ee5 ON dmarc_reports');
        $this->addSql('CREATE INDEX IDX_91BEA3C1115F0EE5 ON dmarc_reports (domain_id)');
        $this->addSql('ALTER TABLE dmarc_reports ADD CONSTRAINT FK_91BEA3C1115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE dmarc_results DROP FOREIGN KEY FK_FF02E0904DFD750C');
        $this->addSql('DROP INDEX idx_9fa3e4144dfd750c ON dmarc_results');
        $this->addSql('CREATE INDEX IDX_FF02E0904DFD750C ON dmarc_results (record_id)');
        $this->addSql('ALTER TABLE dmarc_results ADD CONSTRAINT FK_FF02E0904DFD750C FOREIGN KEY (record_id) REFERENCES dmarc_records (id)');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B424BD2A4C0');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B42A76ED395');
        $this->addSql('DROP INDEX idx_a4520a184bd2a4c0 ON dmarc_seen');
        $this->addSql('CREATE INDEX IDX_40293B424BD2A4C0 ON dmarc_seen (report_id)');
        $this->addSql('DROP INDEX idx_a4520a18a76ed395 ON dmarc_seen');
        $this->addSql('CREATE INDEX IDX_40293B42A76ED395 ON dmarc_seen (user_id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B424BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE domains ADD sts_version VARCHAR(255) DEFAULT \'STSv1\' NOT NULL, ADD sts_mode VARCHAR(255) DEFAULT \'enforce\' NOT NULL, ADD sts_maxage INT DEFAULT 86400 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dmarc_records DROP FOREIGN KEY FK_FC3C5CC24BD2A4C0');
        $this->addSql('DROP INDEX idx_fc3c5cc24bd2a4c0 ON dmarc_records');
        $this->addSql('CREATE INDEX IDX_9C9D58464BD2A4C0 ON dmarc_records (report_id)');
        $this->addSql('ALTER TABLE dmarc_records ADD CONSTRAINT FK_FC3C5CC24BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_reports DROP FOREIGN KEY FK_91BEA3C1115F0EE5');
        $this->addSql('DROP INDEX idx_91bea3c1115f0ee5 ON dmarc_reports');
        $this->addSql('CREATE INDEX IDX_F11FA745115F0EE5 ON dmarc_reports (domain_id)');
        $this->addSql('ALTER TABLE dmarc_reports ADD CONSTRAINT FK_91BEA3C1115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE dmarc_results DROP FOREIGN KEY FK_FF02E0904DFD750C');
        $this->addSql('DROP INDEX idx_ff02e0904dfd750c ON dmarc_results');
        $this->addSql('CREATE INDEX IDX_9FA3E4144DFD750C ON dmarc_results (record_id)');
        $this->addSql('ALTER TABLE dmarc_results ADD CONSTRAINT FK_FF02E0904DFD750C FOREIGN KEY (record_id) REFERENCES dmarc_records (id)');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B424BD2A4C0');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B42A76ED395');
        $this->addSql('DROP INDEX idx_40293b42a76ed395 ON dmarc_seen');
        $this->addSql('CREATE INDEX IDX_A4520A18A76ED395 ON dmarc_seen (user_id)');
        $this->addSql('DROP INDEX idx_40293b424bd2a4c0 ON dmarc_seen');
        $this->addSql('CREATE INDEX IDX_A4520A184BD2A4C0 ON dmarc_seen (report_id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B424BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE domains DROP sts_version, DROP sts_mode, DROP sts_maxage');
    }
}
