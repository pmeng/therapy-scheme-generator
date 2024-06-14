<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240610194810 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scheme ADD title VARCHAR(255) DEFAULT NULL, ADD objective LONGTEXT DEFAULT NULL, ADD place VARCHAR(255) DEFAULT NULL, ADD scheme_date DATETIME DEFAULT NULL, ADD salutation LONGTEXT DEFAULT NULL, ADD footer VARCHAR(255) DEFAULT NULL, ADD free_text JSON DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE scheme DROP title, DROP objective, DROP place, DROP scheme_date, DROP salutation, DROP footer, DROP free_text');
    }
}
