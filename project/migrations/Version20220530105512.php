<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220530105512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE barbecue ADD brochure_filename VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE barbecue DROP target_directory');
        $this->addSql('ALTER TABLE barbecue DROP slugger');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE barbecue ADD target_directory VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE barbecue ADD slugger VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE barbecue DROP brochure_filename');
    }
}
