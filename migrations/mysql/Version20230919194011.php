<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230919194011 extends AbstractMigration
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
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY FK_9C9D58464BD2A4C0');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745115F0EE5');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144DFD750C');
        $this->addSql('ALTER TABLE seen DROP FOREIGN KEY FK_A4520A184BD2A4C0');
        $this->addSql('ALTER TABLE seen DROP FOREIGN KEY FK_A4520A18A76ED395');
        $this->addSql('RENAME TABLE records TO dmarc_records');
        $this->addSql('RENAME TABLE reports TO dmarc_reports');
        $this->addSql('RENAME TABLE results TO dmarc_results');
        $this->addSql('RENAME TABLE seen TO dmarc_seen');
        $this->addSql('CREATE TABLE mxrecords (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_35617F40115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtptls_failure_details (id INT AUTO_INCREMENT NOT NULL, policy_id INT NOT NULL, receiving_mx_hostname_id INT NOT NULL, result_type VARCHAR(255) NOT NULL, sending_mta_ip VARCHAR(255) NOT NULL, receiving_ip VARCHAR(255) NOT NULL, failed_session_count INT NOT NULL, INDEX IDX_81A5B8282D29E3C6 (policy_id), INDEX IDX_81A5B82887B9D47A (receiving_mx_hostname_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtptls_mxrecords (id INT AUTO_INCREMENT NOT NULL, mxrecord_id INT NOT NULL, policy_id INT NOT NULL, priority INT NOT NULL, INDEX IDX_327125CBEADCE1D (mxrecord_id), INDEX IDX_327125C2D29E3C6 (policy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtptls_policies (id INT AUTO_INCREMENT NOT NULL, policy_domain_id INT NOT NULL, report_id INT NOT NULL, policy_type VARCHAR(255) NOT NULL, policy_string_version VARCHAR(255) DEFAULT NULL, policy_string_mode VARCHAR(255) DEFAULT NULL, policy_string_maxage INT DEFAULT NULL, summary_successful_count INT NOT NULL, summary_failed_count INT NOT NULL, INDEX IDX_A5AC55646FCFA580 (policy_domain_id), INDEX IDX_A5AC55644BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtptls_reports (id INT AUTO_INCREMENT NOT NULL, begin_time DATETIME NOT NULL, end_time DATETIME NOT NULL, organisation VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE smtptls_seen (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6AAFE3E94BD2A4C0 (report_id), INDEX IDX_6AAFE3E9A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dmarc_records ADD CONSTRAINT FK_FC3C5CC24BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_reports ADD CONSTRAINT FK_91BEA3C1115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE dmarc_results ADD CONSTRAINT FK_FF02E0904DFD750C FOREIGN KEY (record_id) REFERENCES dmarc_records (id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B424BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id)');
        $this->addSql('ALTER TABLE dmarc_seen ADD CONSTRAINT FK_40293B42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mxrecords ADD CONSTRAINT FK_35617F40115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE smtptls_failure_details ADD CONSTRAINT FK_81A5B8282D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id)');
        $this->addSql('ALTER TABLE smtptls_failure_details ADD CONSTRAINT FK_81A5B82887B9D47A FOREIGN KEY (receiving_mx_hostname_id) REFERENCES mxrecords (id)');
        $this->addSql('ALTER TABLE smtptls_mxrecords ADD CONSTRAINT FK_327125CBEADCE1D FOREIGN KEY (mxrecord_id) REFERENCES mxrecords (id)');
        $this->addSql('ALTER TABLE smtptls_mxrecords ADD CONSTRAINT FK_327125C2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id)');
        $this->addSql('ALTER TABLE smtptls_policies ADD CONSTRAINT FK_A5AC55646FCFA580 FOREIGN KEY (policy_domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE smtptls_policies ADD CONSTRAINT FK_A5AC55644BD2A4C0 FOREIGN KEY (report_id) REFERENCES smtptls_reports (id)');
        $this->addSql('ALTER TABLE smtptls_seen ADD CONSTRAINT FK_6AAFE3E94BD2A4C0 FOREIGN KEY (report_id) REFERENCES smtptls_reports (id)');
        $this->addSql('ALTER TABLE smtptls_seen ADD CONSTRAINT FK_6AAFE3E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dmarc_records DROP FOREIGN KEY FK_FC3C5CC24BD2A4C0');
        $this->addSql('ALTER TABLE dmarc_reports DROP FOREIGN KEY FK_91BEA3C1115F0EE5');
        $this->addSql('ALTER TABLE dmarc_results DROP FOREIGN KEY FK_FF02E0904DFD750C');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B424BD2A4C0');
        $this->addSql('ALTER TABLE dmarc_seen DROP FOREIGN KEY FK_40293B42A76ED395');
        $this->addSql('RENAME TABLE dmarc_records TO records');
        $this->addSql('RENAME TABLE dmarc_reports TO reports');
        $this->addSql('RENAME TABLE dmarc_results TO results');
        $this->addSql('RENAME TABLE dmarc_seen TO seen');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D58464BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144DFD750C FOREIGN KEY (record_id) REFERENCES records (id)');
        $this->addSql('ALTER TABLE seen ADD CONSTRAINT FK_A4520A184BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE seen ADD CONSTRAINT FK_A4520A18A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mxrecords DROP FOREIGN KEY FK_35617F40115F0EE5');
        $this->addSql('ALTER TABLE smtptls_failure_details DROP FOREIGN KEY FK_81A5B8282D29E3C6');
        $this->addSql('ALTER TABLE smtptls_failure_details DROP FOREIGN KEY FK_81A5B82887B9D47A');
        $this->addSql('ALTER TABLE smtptls_mxrecords DROP FOREIGN KEY FK_327125CBEADCE1D');
        $this->addSql('ALTER TABLE smtptls_mxrecords DROP FOREIGN KEY FK_327125C2D29E3C6');
        $this->addSql('ALTER TABLE smtptls_policies DROP FOREIGN KEY FK_A5AC55646FCFA580');
        $this->addSql('ALTER TABLE smtptls_policies DROP FOREIGN KEY FK_A5AC55644BD2A4C0');
        $this->addSql('ALTER TABLE smtptls_seen DROP FOREIGN KEY FK_6AAFE3E94BD2A4C0');
        $this->addSql('ALTER TABLE smtptls_seen DROP FOREIGN KEY FK_6AAFE3E9A76ED395');
        $this->addSql('DROP TABLE mxrecords');
        $this->addSql('DROP TABLE smtptls_failure_details');
        $this->addSql('DROP TABLE smtptls_mxrecords');
        $this->addSql('DROP TABLE smtptls_policies');
        $this->addSql('DROP TABLE smtptls_reports');
        $this->addSql('DROP TABLE smtptls_seen');
    }
}
