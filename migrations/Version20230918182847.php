<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230918182847 extends AbstractMigration
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
        $this->addSql('CREATE TABLE mtasts_mxrecords (id INT AUTO_INCREMENT NOT NULL, mxrecord_id INT NOT NULL, policy_id INT NOT NULL, priority INT NOT NULL, INDEX IDX_9D877D03BEADCE1D (mxrecord_id), INDEX IDX_9D877D032D29E3C6 (policy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mtasts_policies (id INT AUTO_INCREMENT NOT NULL, policy_domain_id INT NOT NULL, report_id INT NOT NULL, policy_type VARCHAR(255) NOT NULL, policy_string_version VARCHAR(255) DEFAULT NULL, policy_string_mode VARCHAR(255) DEFAULT NULL, policy_string_maxage INT DEFAULT NULL, summary_successful_count INT NOT NULL, summary_failed_count INT NOT NULL, INDEX IDX_6156A9636FCFA580 (policy_domain_id), INDEX IDX_6156A9634BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mtasts_reports (id INT AUTO_INCREMENT NOT NULL, begin_time DATETIME NOT NULL, end_time DATETIME NOT NULL, organisation VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE mxrecords (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, name VARCHAR(255) NOT NULL, INDEX IDX_35617F40115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE mtasts_seen ADD CONSTRAINT FK_CC664AEC4BD2A4C0 FOREIGN KEY (report_id) REFERENCES mtasts_reports (id)');
        $this->addSql('ALTER TABLE mtasts_seen ADD CONSTRAINT FK_CC664AECA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE mtasts_mxrecords ADD CONSTRAINT FK_9D877D03BEADCE1D FOREIGN KEY (mxrecord_id) REFERENCES mxrecords (id)');
        $this->addSql('ALTER TABLE mtasts_mxrecords ADD CONSTRAINT FK_9D877D032D29E3C6 FOREIGN KEY (policy_id) REFERENCES mtasts_policies (id)');
        $this->addSql('ALTER TABLE mtasts_policies ADD CONSTRAINT FK_6156A9636FCFA580 FOREIGN KEY (policy_domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE mtasts_policies ADD CONSTRAINT FK_6156A9634BD2A4C0 FOREIGN KEY (report_id) REFERENCES mtasts_reports (id)');
        $this->addSql('ALTER TABLE mxrecords ADD CONSTRAINT FK_35617F40115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE mtasts_mxrecords DROP FOREIGN KEY FK_9D877D03BEADCE1D');
        $this->addSql('ALTER TABLE mtasts_mxrecords DROP FOREIGN KEY FK_9D877D032D29E3C6');
        $this->addSql('ALTER TABLE mtasts_policies DROP FOREIGN KEY FK_6156A9636FCFA580');
        $this->addSql('ALTER TABLE mtasts_policies DROP FOREIGN KEY FK_6156A9634BD2A4C0');
        $this->addSql('ALTER TABLE mxrecords DROP FOREIGN KEY FK_35617F40115F0EE5');
        $this->addSql('ALTER TABLE mtasts_seen DROP FOREIGN KEY FK_CC664AEC4BD2A4C0');
        $this->addSql('ALTER TABLE mtasts_seen DROP FOREIGN KEY FK_CC664AECA76ED395');
        $this->addSql('DROP TABLE mtasts_mxrecords');
        $this->addSql('DROP TABLE mtasts_policies');
        $this->addSql('DROP TABLE mtasts_reports');
        $this->addSql('DROP TABLE mxrecords');
        $this->addSql('DROP TABLE mtasts_seen');
        $this->addSql('RENAME TABLE dmarc_seen TO seen');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
