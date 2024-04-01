<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240320090436 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE stub_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, category_order INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE stub ADD category_id INT DEFAULT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE stub ADD CONSTRAINT FK_FAC29D2512469DE2 FOREIGN KEY (category_id) REFERENCES stub_category (id)');
        $this->addSql('CREATE INDEX IDX_FAC29D2512469DE2 ON stub (category_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stub DROP FOREIGN KEY FK_FAC29D2512469DE2');
        $this->addSql('DROP TABLE stub_category');
        $this->addSql('DROP INDEX IDX_FAC29D2512469DE2 ON stub');
        $this->addSql('ALTER TABLE stub DROP category_id, CHANGE created_at created_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\', CHANGE updated_at updated_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}
