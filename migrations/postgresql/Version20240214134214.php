<?php

declare(strict_types=1);

namespace DoctrineMigrations\PostgreSQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240214134214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE dmarc_records_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dmarc_reports_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE dmarc_results_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE domains_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE logs_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE mxrecords_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE smtptls_failure_details_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE smtptls_mxrecords_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE smtptls_policies_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE smtptls_reports_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE users_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dmarc_records (id INT NOT NULL, report_id INT NOT NULL, source_ip VARCHAR(255) NOT NULL, count INT NOT NULL, policy_disposition INT NOT NULL, policy_dkim VARCHAR(255) NOT NULL, policy_spf VARCHAR(255) NOT NULL, envelope_to VARCHAR(255) DEFAULT NULL, envelope_from VARCHAR(255) DEFAULT NULL, header_from VARCHAR(255) DEFAULT NULL, auth_dkim TEXT DEFAULT NULL, auth_spf TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FC3C5CC24BD2A4C0 ON dmarc_records (report_id)');
        $this->addSql('CREATE TABLE dmarc_reports (id INT NOT NULL, domain_id INT NOT NULL, begin_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, organisation VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, policy_adkim VARCHAR(255) DEFAULT NULL, policy_aspf VARCHAR(255) DEFAULT NULL, policy_p VARCHAR(255) DEFAULT NULL, policy_sp VARCHAR(255) DEFAULT NULL, policy_pct VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_91BEA3C1115F0EE5 ON dmarc_reports (domain_id)');
        $this->addSql('CREATE TABLE dmarc_reports_users (dmarc_reports_id INT NOT NULL, users_id INT NOT NULL, PRIMARY KEY(dmarc_reports_id, users_id))');
        $this->addSql('CREATE INDEX IDX_72F48A4C5FDF4877 ON dmarc_reports_users (dmarc_reports_id)');
        $this->addSql('CREATE INDEX IDX_72F48A4C67B3B43D ON dmarc_reports_users (users_id)');
        $this->addSql('CREATE TABLE dmarc_results (id INT NOT NULL, record_id INT NOT NULL, domain VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, result VARCHAR(255) NOT NULL, dkim_selector VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_FF02E0904DFD750C ON dmarc_results (record_id)');
        $this->addSql('CREATE TABLE domains (id INT NOT NULL, fqdn VARCHAR(255) NOT NULL, sts_version VARCHAR(255) DEFAULT \'STSv1\' NOT NULL, sts_mode VARCHAR(255) DEFAULT \'enforce\' NOT NULL, sts_maxage INT DEFAULT 86400 NOT NULL, mailhost VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C7BBF9DC1A19758 ON domains (fqdn)');
        $this->addSql('CREATE TABLE logs (id INT NOT NULL, time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, message TEXT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE mxrecords (id INT NOT NULL, domain_id INT NOT NULL, name VARCHAR(255) NOT NULL, in_sts BOOLEAN DEFAULT true NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_35617F40115F0EE5 ON mxrecords (domain_id)');
        $this->addSql('CREATE TABLE smtptls_failure_details (id INT NOT NULL, policy_id INT NOT NULL, receiving_mx_hostname_id INT NOT NULL, result_type VARCHAR(255) NOT NULL, sending_mta_ip VARCHAR(255) NOT NULL, receiving_ip VARCHAR(255) NOT NULL, failed_session_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_81A5B8282D29E3C6 ON smtptls_failure_details (policy_id)');
        $this->addSql('CREATE INDEX IDX_81A5B82887B9D47A ON smtptls_failure_details (receiving_mx_hostname_id)');
        $this->addSql('CREATE TABLE smtptls_mxrecords (id INT NOT NULL, mxrecord_id INT NOT NULL, policy_id INT NOT NULL, priority INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_327125CBEADCE1D ON smtptls_mxrecords (mxrecord_id)');
        $this->addSql('CREATE INDEX IDX_327125C2D29E3C6 ON smtptls_mxrecords (policy_id)');
        $this->addSql('CREATE TABLE smtptls_policies (id INT NOT NULL, policy_domain_id INT NOT NULL, report_id INT NOT NULL, policy_type VARCHAR(255) NOT NULL, policy_string_version VARCHAR(255) DEFAULT NULL, policy_string_mode VARCHAR(255) DEFAULT NULL, policy_string_maxage INT DEFAULT NULL, summary_successful_count INT NOT NULL, summary_failed_count INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A5AC55646FCFA580 ON smtptls_policies (policy_domain_id)');
        $this->addSql('CREATE INDEX IDX_A5AC55644BD2A4C0 ON smtptls_policies (report_id)');
        $this->addSql('CREATE TABLE smtptls_reports (id INT NOT NULL, domain_id INT NOT NULL, begin_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, end_time TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, organisation VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_9A97868115F0EE5 ON smtptls_reports (domain_id)');
        $this->addSql('CREATE TABLE smtptls_reports_users (smtptls_reports_id INT NOT NULL, users_id INT NOT NULL, PRIMARY KEY(smtptls_reports_id, users_id))');
        $this->addSql('CREATE INDEX IDX_8C0C024C6869B713 ON smtptls_reports_users (smtptls_reports_id)');
        $this->addSql('CREATE INDEX IDX_8C0C024C67B3B43D ON smtptls_reports_users (users_id)');
        $this->addSql('CREATE TABLE users (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, is_verified BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)');
        $this->addSql('CREATE TABLE users_domains (users_id INT NOT NULL, domains_id INT NOT NULL, PRIMARY KEY(users_id, domains_id))');
        $this->addSql('CREATE INDEX IDX_7C7BCB5767B3B43D ON users_domains (users_id)');
        $this->addSql('CREATE INDEX IDX_7C7BCB573700F4DC ON users_domains (domains_id)');
        $this->addSql('ALTER TABLE dmarc_records ADD CONSTRAINT FK_FC3C5CC24BD2A4C0 FOREIGN KEY (report_id) REFERENCES dmarc_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dmarc_reports ADD CONSTRAINT FK_91BEA3C1115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_72F48A4C5FDF4877 FOREIGN KEY (dmarc_reports_id) REFERENCES dmarc_reports (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dmarc_reports_users ADD CONSTRAINT FK_72F48A4C67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE dmarc_results ADD CONSTRAINT FK_FF02E0904DFD750C FOREIGN KEY (record_id) REFERENCES dmarc_records (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE mxrecords ADD CONSTRAINT FK_35617F40115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_failure_details ADD CONSTRAINT FK_81A5B8282D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_failure_details ADD CONSTRAINT FK_81A5B82887B9D47A FOREIGN KEY (receiving_mx_hostname_id) REFERENCES mxrecords (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_mxrecords ADD CONSTRAINT FK_327125CBEADCE1D FOREIGN KEY (mxrecord_id) REFERENCES mxrecords (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_mxrecords ADD CONSTRAINT FK_327125C2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_policies ADD CONSTRAINT FK_A5AC55646FCFA580 FOREIGN KEY (policy_domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_policies ADD CONSTRAINT FK_A5AC55644BD2A4C0 FOREIGN KEY (report_id) REFERENCES smtptls_reports (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_reports ADD CONSTRAINT FK_9A97868115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_8C0C024C6869B713 FOREIGN KEY (smtptls_reports_id) REFERENCES smtptls_reports (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE smtptls_reports_users ADD CONSTRAINT FK_8C0C024C67B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_domains ADD CONSTRAINT FK_7C7BCB5767B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE users_domains ADD CONSTRAINT FK_7C7BCB573700F4DC FOREIGN KEY (domains_id) REFERENCES domains (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE dmarc_records_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dmarc_reports_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE dmarc_results_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE domains_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE logs_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE mxrecords_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE smtptls_failure_details_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE smtptls_mxrecords_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE smtptls_policies_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE smtptls_reports_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE users_id_seq CASCADE');
        $this->addSql('ALTER TABLE dmarc_records DROP CONSTRAINT FK_FC3C5CC24BD2A4C0');
        $this->addSql('ALTER TABLE dmarc_reports DROP CONSTRAINT FK_91BEA3C1115F0EE5');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP CONSTRAINT FK_72F48A4C5FDF4877');
        $this->addSql('ALTER TABLE dmarc_reports_users DROP CONSTRAINT FK_72F48A4C67B3B43D');
        $this->addSql('ALTER TABLE dmarc_results DROP CONSTRAINT FK_FF02E0904DFD750C');
        $this->addSql('ALTER TABLE mxrecords DROP CONSTRAINT FK_35617F40115F0EE5');
        $this->addSql('ALTER TABLE smtptls_failure_details DROP CONSTRAINT FK_81A5B8282D29E3C6');
        $this->addSql('ALTER TABLE smtptls_failure_details DROP CONSTRAINT FK_81A5B82887B9D47A');
        $this->addSql('ALTER TABLE smtptls_mxrecords DROP CONSTRAINT FK_327125CBEADCE1D');
        $this->addSql('ALTER TABLE smtptls_mxrecords DROP CONSTRAINT FK_327125C2D29E3C6');
        $this->addSql('ALTER TABLE smtptls_policies DROP CONSTRAINT FK_A5AC55646FCFA580');
        $this->addSql('ALTER TABLE smtptls_policies DROP CONSTRAINT FK_A5AC55644BD2A4C0');
        $this->addSql('ALTER TABLE smtptls_reports DROP CONSTRAINT FK_9A97868115F0EE5');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP CONSTRAINT FK_8C0C024C6869B713');
        $this->addSql('ALTER TABLE smtptls_reports_users DROP CONSTRAINT FK_8C0C024C67B3B43D');
        $this->addSql('ALTER TABLE users_domains DROP CONSTRAINT FK_7C7BCB5767B3B43D');
        $this->addSql('ALTER TABLE users_domains DROP CONSTRAINT FK_7C7BCB573700F4DC');
        $this->addSql('DROP TABLE dmarc_records');
        $this->addSql('DROP TABLE dmarc_reports');
        $this->addSql('DROP TABLE dmarc_reports_users');
        $this->addSql('DROP TABLE dmarc_results');
        $this->addSql('DROP TABLE domains');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE mxrecords');
        $this->addSql('DROP TABLE smtptls_failure_details');
        $this->addSql('DROP TABLE smtptls_mxrecords');
        $this->addSql('DROP TABLE smtptls_policies');
        $this->addSql('DROP TABLE smtptls_reports');
        $this->addSql('DROP TABLE smtptls_reports_users');
        $this->addSql('DROP TABLE users');
        $this->addSql('DROP TABLE users_domains');
    }
}
