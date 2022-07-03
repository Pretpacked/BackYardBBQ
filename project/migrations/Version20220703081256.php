<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703081256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE accessoire (id INT NOT NULL, name VARCHAR(255) NOT NULL, description TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE barbecue (id INT NOT NULL, image VARCHAR(255) NOT NULL, name VARCHAR(255) DEFAULT NULL, description TEXT NOT NULL, barbecue_price INT NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, name VARCHAR(255) NOT NULL, adress VARCHAR(255) NOT NULL, phone_number INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, customer_id INT NOT NULL, barbecue_id INT NOT NULL, orderd_date DATE NOT NULL, start_date DATE NOT NULL, end_date DATE NOT NULL, price_total INT NOT NULL, remark TEXT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F52993989395C3F3 ON "order" (customer_id)');
        $this->addSql('CREATE INDEX IDX_F5299398E2A5D7D4 ON "order" (barbecue_id)');
        $this->addSql('CREATE TABLE order_accessoire (order_id INT NOT NULL, accessoire_id INT NOT NULL, PRIMARY KEY(order_id, accessoire_id))');
        $this->addSql('CREATE INDEX IDX_BB27FF3F8D9F6D38 ON order_accessoire (order_id)');
        $this->addSql('CREATE INDEX IDX_BB27FF3FD23B67ED ON order_accessoire (accessoire_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398E2A5D7D4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_accessoire ADD CONSTRAINT FK_BB27FF3F8D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_accessoire ADD CONSTRAINT FK_BB27FF3FD23B67ED FOREIGN KEY (accessoire_id) REFERENCES accessoire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_accessoire DROP CONSTRAINT FK_BB27FF3FD23B67ED');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F5299398E2A5D7D4');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT FK_F52993989395C3F3');
        $this->addSql('ALTER TABLE order_accessoire DROP CONSTRAINT FK_BB27FF3F8D9F6D38');
        $this->addSql('DROP TABLE accessoire');
        $this->addSql('DROP TABLE barbecue');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_accessoire');
        $this->addSql('DROP TABLE "user"');
    }
}