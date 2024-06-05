<?php

declare(strict_types=1);

namespace DoctrineMigrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214121100 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dmarc_seen RENAME COLUMN report_id TO dmarc_reports_id');
        $this->addSql('ALTER TABLE dmarc_seen RENAME TO dmarc_reports_users');

        $this->addSql('ALTER TABLE dmarc_reports_users MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP FOREIGN KEY FK_40293B42A76ED395');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP FOREIGN KEY FK_40293B424BD2A4C0');
        $this->addSql('DROP INDEX IDX_40293B42A76ED395 ON dmarc_reports_users');
        $this->addSql('DROP INDEX `primary` ON dmarc_reports_users');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP id, CHANGE user_id users_id INT NOT NULL');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_72F48A4C5FDF4877 FOREIGN KEY (dmarc_reports_id) REFERENCES dmarc_reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_72F48A4C67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_72F48A4C67B3B43D ON dmarc_reports_users (users_id)');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD PRIMARY KEY (dmarc_reports_id, users_id)');
        $this->addSql('ALTER TABLE dmarc_reports_users RENAME INDEX idx_40293b424bd2a4c0 TO IDX_72F48A4C5FDF4877');

        $this->addSql('ALTER TABLE smtptls_seen RENAME COLUMN report_id TO smtptls_reports_id');
        $this->addSql('ALTER TABLE smtptls_seen RENAME TO smtptls_reports_users');

        $this->addSql('ALTER TABLE smtptls_reports_users MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP FOREIGN KEY FK_6AAFE3E9A76ED395');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP FOREIGN KEY FK_6AAFE3E94BD2A4C0');
        $this->addSql('DROP INDEX IDX_6AAFE3E9A76ED395 ON smtptls_reports_users');
        $this->addSql('DROP INDEX `primary` ON smtptls_reports_users');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP id, CHANGE user_id users_id INT NOT NULL');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_8C0C024C6869B713 FOREIGN KEY (smtptls_reports_id) REFERENCES smtptls_reports (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_8C0C024C67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_8C0C024C67B3B43D ON smtptls_reports_users (users_id)');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD PRIMARY KEY (smtptls_reports_id, users_id)');
        $this->addSql('ALTER TABLE smtptls_reports_users RENAME INDEX idx_6aafe3e94bd2a4c0 TO IDX_8C0C024C6869B713');

        $this->addSql('ALTER TABLE smtptls_reports ADD domain_id INT NOT NULL AFTER id');
        $this->addSql('UPDATE smtptls_reports r
        JOIN (
            SELECT r.id AS report_id, p.policy_domain_id
            FROM smtptls_reports r
            JOIN smtptls_policies p ON r.id = p.report_id
        ) AS subquery ON r.id = subquery.report_id
        SET r.domain_id = subquery.policy_domain_id;');

        $this->addSql('ALTER TABLE smtptls_reports ADD CONSTRAINT FK_9A97868115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('CREATE INDEX IDX_9A97868115F0EE5 ON smtptls_reports (domain_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dmarc_reports_users DROP FOREIGN KEY FK_72F48A4C5FDF4877');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP FOREIGN KEY FK_72F48A4C67B3B43D');
        $this->addSql('DROP INDEX IDX_72F48A4C67B3B43D ON dmarc_reports_users');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD id INT AUTO_INCREMENT NOT NULL, CHANGE users_id user_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_40293B42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_40293B424BD2A4C0 FOREIGN KEY (dmarc_reports_id) REFERENCES dmarc_reports (id)');
        $this->addSql('CREATE INDEX IDX_40293B42A76ED395 ON dmarc_reports_users (user_id)');
        $this->addSql('ALTER TABLE dmarc_reports_users RENAME INDEX idx_72f48a4c5fdf4877 TO IDX_40293B424BD2A4C0');

        $this->addSql('ALTER TABLE dmarc_reports_users RENAME COLUMN dmarc_reports_id TO report_id');
        $this->addSql('ALTER TABLE dmarc_reports_users RENAME TO dmarc_seen');

        $this->addSql('ALTER TABLE smtptls_reports_users DROP FOREIGN KEY FK_8C0C024C6869B713');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP FOREIGN KEY FK_8C0C024C67B3B43D');
        $this->addSql('DROP INDEX IDX_8C0C024C67B3B43D ON smtptls_reports_users');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD id INT AUTO_INCREMENT NOT NULL, CHANGE users_id user_id INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_6AAFE3E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_6AAFE3E94BD2A4C0 FOREIGN KEY (smtptls_reports_id) REFERENCES smtptls_reports (id)');
        $this->addSql('CREATE INDEX IDX_6AAFE3E9A76ED395 ON smtptls_reports_users (user_id)');
        $this->addSql('ALTER TABLE smtptls_reports_users RENAME INDEX idx_8c0c024c6869b713 TO IDX_6AAFE3E94BD2A4C0');

        $this->addSql('ALTER TABLE smtptls_reports_users RENAME COLUMN smtptls_reports_id TO report_id');
        $this->addSql('ALTER TABLE smtptls_reports_users RENAME TO smtptls_seen');

        $this->addSql('ALTER TABLE smtptls_reports DROP FOREIGN KEY FK_9A97868115F0EE5');
        $this->addSql('DROP INDEX IDX_9A97868115F0EE5 ON smtptls_reports');
        $this->addSql('ALTER TABLE smtptls_reports DROP domain_id');
    }
}
