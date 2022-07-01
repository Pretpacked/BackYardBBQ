<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220701072222 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE accessoire_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE accessoire (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE accessoire_customer (accessoire_id INT NOT NULL, customer_id INT NOT NULL, PRIMARY KEY(accessoire_id, customer_id))');
        $this->addSql('CREATE INDEX IDX_CD6B1E5AD23B67ED ON accessoire_customer (accessoire_id)');
        $this->addSql('CREATE INDEX IDX_CD6B1E5A9395C3F3 ON accessoire_customer (customer_id)');
        $this->addSql('ALTER TABLE accessoire_customer ADD CONSTRAINT FK_CD6B1E5AD23B67ED FOREIGN KEY (accessoire_id) REFERENCES accessoire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accessoire_customer ADD CONSTRAINT FK_CD6B1E5A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE accessoire_customer DROP CONSTRAINT FK_CD6B1E5AD23B67ED');
        $this->addSql('DROP SEQUENCE accessoire_id_seq CASCADE');
        $this->addSql('DROP TABLE accessoire');
        $this->addSql('DROP TABLE accessoire_customer');
    }
}
