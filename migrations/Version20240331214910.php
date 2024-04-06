<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240331214910 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Drop existing categories and assign a new category to stubs';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('UPDATE stub SET category_id = NULL');

        // Drop existing categories
        $this->addSql('DELETE FROM stub_category');

        // Add a new category
        $this->addSql("INSERT INTO stub_category (short_name, report_name, category_order) VALUES ('Default Category', 'Default Category', 1)");

        // Update existing stubs to the new category
        $this->addSql('UPDATE stub SET category_id = (SELECT id FROM stub_category WHERE short_name = \'Default Category\')');
    }

    public function down(Schema $schema): void
    {
        // This down() migration would not restore the deleted categories as it would require knowing their original state
        // It's recommended to add a manual down migration if restoring the original state is necessary
    }
}
