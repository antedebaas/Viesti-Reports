<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240302155214 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE smtptls_rdata_records_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE smtptls_rdata_records (id INT NOT NULL, policy_id INT NOT NULL, usagetype INT NOT NULL, selectortype INT NOT NULL, matchingtype INT NOT NULL, data TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_65C68ADB2D29E3C6 ON smtptls_rdata_records (policy_id)');
        $this->addSql('ALTER TABLE smtptls_rdata_records ADD CONSTRAINT FK_65C68ADB2D29E3C6 FOREIGN KEY (policy_id) REFERENCES smtptls_policies (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE smtptls_rdata_records_id_seq CASCADE');
        $this->addSql('ALTER TABLE smtptls_rdata_records DROP CONSTRAINT FK_65C68ADB2D29E3C6');
        $this->addSql('DROP TABLE smtptls_rdata_records');
    }
}
