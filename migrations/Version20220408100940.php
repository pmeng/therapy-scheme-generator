<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220408100940 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE label_stub (label_id INT NOT NULL, stub_id INT NOT NULL, INDEX IDX_36FA08E933B92F39 (label_id), INDEX IDX_36FA08E9E942160A (stub_id), PRIMARY KEY(label_id, stub_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE label_stub ADD CONSTRAINT FK_36FA08E933B92F39 FOREIGN KEY (label_id) REFERENCES label (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE label_stub ADD CONSTRAINT FK_36FA08E9E942160A FOREIGN KEY (stub_id) REFERENCES stub (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE label_stub');
    }
}
