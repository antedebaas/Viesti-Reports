<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240124174018 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE users_domains (users_id INT NOT NULL, domains_id INT NOT NULL, INDEX IDX_7C7BCB5767B3B43D (users_id), INDEX IDX_7C7BCB573700F4DC (domains_id), PRIMARY KEY(users_id, domains_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE users_domains ADD CONSTRAINT FK_7C7BCB5767B3B43D FOREIGN KEY (users_id) REFERENCES users (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE users_domains ADD CONSTRAINT FK_7C7BCB573700F4DC FOREIGN KEY (domains_id) REFERENCES domains (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users_domains DROP FOREIGN KEY FK_7C7BCB5767B3B43D');
        $this->addSql('ALTER TABLE users_domains DROP FOREIGN KEY FK_7C7BCB573700F4DC');
        $this->addSql('DROP TABLE users_domains');
    }
}
