<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220509101741 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE barbecue_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE barbecue (id INT NOT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT NOT NULL, barbecue_price INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, name VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, phone_number INT NOT NULL, orderd_date DATE NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, price_total INT NOT NULL, remark TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer_barbecue (customer_id INT NOT NULL, barbecue_id INT NOT NULL, PRIMARY KEY(customer_id, barbecue_id))');
        $this->addSql('CREATE INDEX IDX_7A099E1A9395C3F3 ON customer_barbecue (customer_id)');
        $this->addSql('CREATE INDEX IDX_7A099E1AE2A5D7D4 ON customer_barbecue (barbecue_id)');
        $this->addSql('ALTER TABLE customer_barbecue ADD CONSTRAINT FK_7A099E1A9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_barbecue ADD CONSTRAINT FK_7A099E1AE2A5D7D4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer_barbecue DROP CONSTRAINT FK_7A099E1AE2A5D7D4');
        $this->addSql('ALTER TABLE customer_barbecue DROP CONSTRAINT FK_7A099E1A9395C3F3');
        $this->addSql('DROP SEQUENCE barbecue_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE customer_id_seq CASCADE');
        $this->addSql('DROP TABLE barbecue');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE customer_barbecue');
    }
}
