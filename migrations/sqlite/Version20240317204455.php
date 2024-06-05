<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240317204455 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__logs AS SELECT id, time, message, success FROM logs');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL, success BOOLEAN NOT NULL)');
        $this->addSql('INSERT INTO logs (id, time, message, success) SELECT id, time, message, success FROM __temp__logs');
        $this->addSql('DROP TABLE __temp__logs');
        $this->addSql('CREATE TEMPORARY TABLE __temp__smtptls_failure_details AS SELECT id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count FROM smtptls_failure_details');
        $this->addSql('DROP TABLE smtptls_failure_details');
        $this->addSql('CREATE TABLE smtptls_failure_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_id INTEGER NOT NULL, receiving_mx_hostname_id INTEGER NOT NULL, result_type VARCHAR(255) NOT NULL, sending_mta_ip VARCHAR(255) NOT NULL, receiving_ip VARCHAR(255) DEFAULT NULL, failed_session_count INTEGER NOT NULL, CONSTRAINT FK_81A5B8282D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_81A5B82887B9D47A FOREIGN KEY (receiving_mx_hostname_id) REFERENCES mxrecords (id) ON UPDATE NO ACTION ON DELETE NO ACTION NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO smtptls_failure_details (id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count) SELECT id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count FROM __temp__smtptls_failure_details');
        $this->addSql('DROP TABLE __temp__smtptls_failure_details');
        $this->addSql('CREATE INDEX IDX_81A5B82887B9D47A ON smtptls_failure_details (receiving_mx_hostname_id)');
        $this->addSql('CREATE INDEX IDX_81A5B8282D29E3C6 ON smtptls_failure_details (policy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__logs AS SELECT id, time, message, success FROM logs');
        $this->addSql('DROP TABLE logs');
        $this->addSql('CREATE TABLE logs (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, time DATETIME NOT NULL, message CLOB DEFAULT NULL, success BOOLEAN DEFAULT true NOT NULL)');
        $this->addSql('INSERT INTO logs (id, time, message, success) SELECT id, time, message, success FROM __temp__logs');
        $this->addSql('DROP TABLE __temp__logs');
        $this->addSql('CREATE TEMPORARY TABLE __temp__smtptls_failure_details AS SELECT id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count FROM smtptls_failure_details');
        $this->addSql('DROP TABLE smtptls_failure_details');
        $this->addSql('CREATE TABLE smtptls_failure_details (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, policy_id INTEGER NOT NULL, receiving_mx_hostname_id INTEGER NOT NULL, result_type VARCHAR(255) NOT NULL, sending_mta_ip VARCHAR(255) NOT NULL, receiving_ip VARCHAR(255) NOT NULL, failed_session_count INTEGER NOT NULL, CONSTRAINT FK_81A5B8282D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_81A5B82887B9D47A FOREIGN KEY (receiving_mx_hostname_id) REFERENCES mxrecords (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO smtptls_failure_details (id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count) SELECT id, policy_id, receiving_mx_hostname_id, result_type, sending_mta_ip, receiving_ip, failed_session_count FROM __temp__smtptls_failure_details');
        $this->addSql('DROP TABLE __temp__smtptls_failure_details');
        $this->addSql('CREATE INDEX IDX_81A5B8282D29E3C6 ON smtptls_failure_details (policy_id)');
        $this->addSql('CREATE INDEX IDX_81A5B82887B9D47A ON smtptls_failure_details (receiving_mx_hostname_id)');
    }
}
