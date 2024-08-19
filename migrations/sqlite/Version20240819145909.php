<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240819145909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE domains ADD COLUMN bimiselector VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE domains ADD COLUMN dkimselector VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__domains AS SELECT id, fqdn, sts_version, sts_mode, sts_maxage, mailhost, bimisvgfile, bimivmcfile FROM domains');
        $this->addSql('DROP TABLE domains');
        $this->addSql('CREATE TABLE domains (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, fqdn VARCHAR(255) NOT NULL, sts_version VARCHAR(255) DEFAULT \'STSv1\' NOT NULL, sts_mode VARCHAR(255) DEFAULT \'enforce\' NOT NULL, sts_maxage INTEGER DEFAULT 86400 NOT NULL, mailhost VARCHAR(255) NOT NULL, bimisvgfile CLOB DEFAULT NULL, bimivmcfile CLOB DEFAULT NULL)');
        $this->addSql('INSERT INTO domains (id, fqdn, sts_version, sts_mode, sts_maxage, mailhost, bimisvgfile, bimivmcfile) SELECT id, fqdn, sts_version, sts_mode, sts_maxage, mailhost, bimisvgfile, bimivmcfile FROM __temp__domains');
        $this->addSql('DROP TABLE __temp__domains');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8C7BBF9DC1A19758 ON domains (fqdn)');
    }
}
