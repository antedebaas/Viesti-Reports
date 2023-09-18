<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230809184012 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE domains (id INT AUTO_INCREMENT NOT NULL, fqdn VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE logs (id INT AUTO_INCREMENT NOT NULL, time DATETIME NOT NULL, message LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE records (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, source_ip VARCHAR(255) NOT NULL, count INT NOT NULL, policy_disposition INT NOT NULL, policy_dkim VARCHAR(255) NOT NULL, policy_spf VARCHAR(255) NOT NULL, envelope_to VARCHAR(255) DEFAULT NULL, envelope_from VARCHAR(255) DEFAULT NULL, header_from VARCHAR(255) DEFAULT NULL, auth_dkim LONGTEXT DEFAULT NULL, auth_spf LONGTEXT DEFAULT NULL, INDEX IDX_9C9D58464BD2A4C0 (report_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reports (id INT AUTO_INCREMENT NOT NULL, domain_id INT NOT NULL, begin_time DATETIME NOT NULL, end_time DATETIME NOT NULL, organisation VARCHAR(255) NOT NULL, external_id VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, contact_info VARCHAR(255) NOT NULL, policy_adkim VARCHAR(255) DEFAULT NULL, policy_aspf VARCHAR(255) DEFAULT NULL, policy_p VARCHAR(255) DEFAULT NULL, policy_sp VARCHAR(255) DEFAULT NULL, policy_pct VARCHAR(255) DEFAULT NULL, INDEX IDX_F11FA745115F0EE5 (domain_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE results (id INT AUTO_INCREMENT NOT NULL, record_id INT NOT NULL, domain VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, result VARCHAR(255) NOT NULL, dkim_selector VARCHAR(255) DEFAULT NULL, INDEX IDX_9FA3E4144DFD750C (record_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE seen (id INT AUTO_INCREMENT NOT NULL, report_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_A4520A184BD2A4C0 (report_id), INDEX IDX_A4520A18A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE records ADD CONSTRAINT FK_9C9D58464BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE reports ADD CONSTRAINT FK_F11FA745115F0EE5 FOREIGN KEY (domain_id) REFERENCES domains (id)');
        $this->addSql('ALTER TABLE results ADD CONSTRAINT FK_9FA3E4144DFD750C FOREIGN KEY (record_id) REFERENCES records (id)');
        $this->addSql('ALTER TABLE seen ADD CONSTRAINT FK_A4520A184BD2A4C0 FOREIGN KEY (report_id) REFERENCES reports (id)');
        $this->addSql('ALTER TABLE seen ADD CONSTRAINT FK_A4520A18A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE records DROP FOREIGN KEY FK_9C9D58464BD2A4C0');
        $this->addSql('ALTER TABLE reports DROP FOREIGN KEY FK_F11FA745115F0EE5');
        $this->addSql('ALTER TABLE results DROP FOREIGN KEY FK_9FA3E4144DFD750C');
        $this->addSql('ALTER TABLE seen DROP FOREIGN KEY FK_A4520A184BD2A4C0');
        $this->addSql('ALTER TABLE seen DROP FOREIGN KEY FK_A4520A18A76ED395');
        $this->addSql('DROP TABLE domains');
        $this->addSql('DROP TABLE logs');
        $this->addSql('DROP TABLE records');
        $this->addSql('DROP TABLE reports');
        $this->addSql('DROP TABLE results');
        $this->addSql('DROP TABLE seen');
        $this->addSql('DROP TABLE users');
    }

    public function isTransactional(): bool
    {
        return false;
    }
}
