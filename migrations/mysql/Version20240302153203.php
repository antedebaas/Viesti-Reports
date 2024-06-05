<?php

declare(strict_types=1);

namespace DoctrineMigrations\MySQL;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302153203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE smtptls_rdata_records (id INT AUTO_INCREMENT NOT NULL, policy_id INT NOT NULL, usagetype INT NOT NULL, selectortype INT NOT NULL, matchingtype INT NOT NULL, data LONGTEXT NOT NULL, INDEX IDX_65C68ADB2D29E3C6 (policy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE smtptls_rdata_records ADD CONSTRAINT FK_65C68ADB2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE smtptls_rdata_records DROP FOREIGN KEY FK_65C68ADB2D29E3C6');
        $this->addSql('DROP TABLE smtptls_rdata_records');
    }
}
