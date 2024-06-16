<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240615200713 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE scheme_setting (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) DEFAULT NULL, objective LONGTEXT DEFAULT NULL, place VARCHAR(255) DEFAULT NULL, scheme_date DATETIME DEFAULT NULL, salutation LONGTEXT DEFAULT NULL, footer VARCHAR(255) DEFAULT NULL, text_font_style VARCHAR(255) DEFAULT NULL, title_font_style VARCHAR(255) DEFAULT NULL, heading_font_style VARCHAR(255) DEFAULT NULL, text_font_size INT DEFAULT NULL, title_font_size INT DEFAULT NULL, heading_font_size INT DEFAULT NULL, logo LONGBLOB DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE scheme_setting');
    }
}
