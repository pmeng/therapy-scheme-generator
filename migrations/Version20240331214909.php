<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331214909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stub_category ADD short_name VARCHAR(50) NOT NULL, ADD report_name VARCHAR(255) DEFAULT NULL, DROP name');

        // Add a default category
        $this->addSql("INSERT INTO stub_category (`short_name`, `report_name`, `category_order`) VALUES ('Default Category', 'Default Category', 1)");

        // Update existing stubs to the default category
        $this->addSql('UPDATE stub SET category_id = (SELECT id FROM stub_category WHERE name = \'Default Category\') WHERE category_id IS NULL');

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE stub_category ADD name VARCHAR(255) NOT NULL, DROP short_name, DROP report_name');
    }
}
