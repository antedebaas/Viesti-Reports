<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240204190327 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dmarc_records (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, report_id INTEGER NOT NULL, source_ip VARCHAR(255) NOT NULL, count INTEGER NOT NULL, policy_disposition INTEGER NOT NULL, policy_dkim VARCHAR(255) NOT NULL, policy_spf VARCHAR(255) NOT NULL, envelope_to VARCHAR(255) DEFAULT NULL, envelope_from VARCHAR(255) DEFAULT NULL, header_from VARCHAR(255) DEFAULT NULL, auth_dkim CLOB DEFAULT NULL, auth_spf CLOB DEFAULT NULL, CONSTRAINT FK_FC3C5CC24BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FC3C5CC24BD2A4C0 ON dmarc_records (report_id)');
        $this->addSql('CREATE TABLE dmarc_reports (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain_id INTEGER NOT NULL, begin_time DATETIME NOT NULL, end_time DATETIME NOT NULL, organisation VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, policy_adkim VARCHAR(255) DEFAULT NULL, policy_aspf VARCHAR(255) DEFAULT NULL, policy_p VARCHAR(255) DEFAULT NULL, policy_sp VARCHAR(255) DEFAULT NULL, policy_pct VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_91BEA3C1115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_91BEA3C1115F0EE5 ON dmarc_reports (domain_id)');
        $this->addSql('CREATE TABLE dmarc_results (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, record_id INTEGER NOT NULL, domain VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, result VARCHAR(255) NOT NULL, dkim_selector VARCHAR(255) DEFAULT NULL, CONSTRAINT FK_FF02E0904DFD750C FOREIGN KEY (record_id) REFERENCES dmarc_records (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_FF02E0904DFD750C ON dmarc_results (record_id)');
        $this->addSql('CREATE TABLE dmarc_seen (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, report_id INTEGER NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_40293B424BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_40293B42A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_40293B424BD2A4C0 ON dmarc_seen (report_id)');
        $this->addSql('CREATE INDEX IDX_40293B42A76ED395 ON dmarc_seen (user_id)');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fqdn VARCHAR(255) NOT NULL, sts_version VARCHAR(255) DEFAULT \'STSv1\' NOT NULL, sts_mode VARCHAR(255) DEFAULT \'enforce\' NOT NULL, sts_maxage INTEGER DEFAULT 86400 NOT NULL, mailhost VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C7BBF9DC1A19758 ON domains (fqdn)');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL)');
        $this->addSql('CREATE TABLE mxrecords (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, domain_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL, in_sts BOOLEAN DEFAULT 1 NOT NULL, CONSTRAINT FK_35617F40115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_35617F40115F0EE5 ON mxrecords (domain_id)');
        $this->addSql('CREATE TABLE smtptls_failure_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_id INTEGER NOT NULL, receiving_mx_hostname_id INTEGER NOT NULL, result_type VARCHAR(255) NOT NULL, sending_mta_ip VARCHAR(255) NOT NULL, receiving_ip VARCHAR(255) NOT NULL, failed_session_count INTEGER NOT NULL, CONSTRAINT FK_81A5B8282D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_81A5B82887B9D47A FOREIGN KEY (receiving_mx_hostname_id) REFERENCES mxrecords (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_81A5B8282D29E3C6 ON smtptls_failure_details (policy_id)');
        $this->addSql('CREATE INDEX IDX_81A5B82887B9D47A ON smtptls_failure_details (receiving_mx_hostname_id)');
        $this->addSql('CREATE TABLE smtptls_mxrecords (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, mxrecord_id INTEGER NOT NULL, policy_id INTEGER NOT NULL, priority INTEGER NOT NULL, CONSTRAINT FK_327125CBEADCE1D FOREIGN KEY (mxrecord_id) REFERENCES mxrecords (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_327125C2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_327125CBEADCE1D ON smtptls_mxrecords (mxrecord_id)');
        $this->addSql('CREATE INDEX IDX_327125C2D29E3C6 ON smtptls_mxrecords (policy_id)');
        $this->addSql('CREATE TABLE smtptls_policies (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_domain_id INTEGER NOT NULL, report_id INTEGER NOT NULL, policy_type VARCHAR(255) NOT NULL, policy_string_version VARCHAR(255) DEFAULT NULL, policy_string_mode VARCHAR(255) DEFAULT NULL, policy_string_maxage INTEGER DEFAULT NULL, summary_successful_count INTEGER NOT NULL, summary_failed_count INTEGER NOT NULL, CONSTRAINT FK_A5AC55646FCFA580 FOREIGN KEY (policy_domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_A5AC55644BD2A4C0 FOREIGN KEY (report_id) REFERENCES smtptls_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_A5AC55646FCFA580 ON smtptls_policies (policy_domain_id)');
        $this->addSql('CREATE INDEX IDX_A5AC55644BD2A4C0 ON smtptls_policies (report_id)');
        $this->addSql('CREATE TABLE smtptls_reports (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, begin_time DATETIME NOT NULL, end_time DATETIME NOT NULL, organisation VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE TABLE smtptls_seen (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, report_id INTEGER NOT NULL, user_id INTEGER NOT NULL, CONSTRAINT FK_6AAFE3E94BD2A4C0 FOREIGN KEY (report_id) REFERENCES smtptls_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_6AAFE3E9A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_6AAFE3E94BD2A4C0 ON smtptls_seen (report_id)');
        $this->addSql('CREATE INDEX IDX_6AAFE3E9A76ED395 ON smtptls_seen (user_id)');
        $this->addSql('CREATE TABLE users (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles CLOB NOT NULL --(DC2Type:json)
        , password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE users_domains (users_id INTEGER NOT NULL, domains_id INTEGER NOT NULL, PRIMARY KEY(users_id, domains_id), CONSTRAINT FK_7C7BCB5767B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_7C7BCB573700F4DC FOREIGN KEY (domains_id) REFERENCES domains (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('CREATE INDEX IDX_7C7BCB5767B3B43D ON users_domains (users_id)');
        $this->addSql('CREATE INDEX IDX_7C7BCB573700F4DC ON users_domains (domains_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE dmarc_records');
        $this->addSql('DROP TABLE dmarc_reports');
        $this->addSql('DROP TABLE dmarc_results');
        $this->addSql('DROP TABLE dmarc_seen');
        $this->addSql('DROP TABLE domains');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE mxrecords');
        $this->addSql('DROP TABLE smtptls_failure_details');
        $this->addSql('DROP TABLE smtptls_mxrecords');
        $this->addSql('DROP TABLE smtptls_policies');
        $this->addSql('DROP TABLE smtptls_reports');
        $this->addSql('DROP TABLE smtptls_seen');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_domains');
    }
}
