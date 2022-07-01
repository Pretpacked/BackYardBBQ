<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220701090024 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE "order_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE "order" (id INT NOT NULL, customer_id INT NOT NULL, barbecue_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_F52993989395C3F3 ON "order" (customer_id)');
        $this->addSql('CREATE INDEX IDX_F5299398E2A5D7D4 ON "order" (barbecue_id)');
        $this->addSql('CREATE TABLE order_accessoire (order_id INT NOT NULL, accessoire_id INT NOT NULL, PRIMARY KEY(order_id, accessoire_id))');
        $this->addSql('CREATE INDEX IDX_BB27FF3F8D9F6D38 ON order_accessoire (order_id)');
        $this->addSql('CREATE INDEX IDX_BB27FF3FD23B67ED ON order_accessoire (accessoire_id)');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F52993989395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT FK_F5299398E2A5D7D4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_accessoire ADD CONSTRAINT FK_BB27FF3F8D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_accessoire ADD CONSTRAINT FK_BB27FF3FD23B67ED FOREIGN KEY (accessoire_id) REFERENCES accessoire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE customer_barbecue');
        $this->addSql('DROP TABLE accessoire_customer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE order_accessoire DROP CONSTRAINT FK_BB27FF3F8D9F6D38');
        $this->addSql('DROP SEQUENCE "order_id_seq" CASCADE');
        $this->addSql('CREATE TABLE customer_barbecue (customer_id INT NOT NULL, barbecue_id INT NOT NULL, PRIMARY KEY(customer_id, barbecue_id))');
        $this->addSql('CREATE INDEX idx_7a099e1ae2a5d7d4 ON customer_barbecue (barbecue_id)');
        $this->addSql('CREATE INDEX idx_7a099e1a9395c3f3 ON customer_barbecue (customer_id)');
        $this->addSql('CREATE TABLE accessoire_customer (accessoire_id INT NOT NULL, customer_id INT NOT NULL, PRIMARY KEY(accessoire_id, customer_id))');
        $this->addSql('CREATE INDEX idx_cd6b1e5ad23b67ed ON accessoire_customer (accessoire_id)');
        $this->addSql('CREATE INDEX idx_cd6b1e5a9395c3f3 ON accessoire_customer (customer_id)');
        $this->addSql('ALTER TABLE customer_barbecue ADD CONSTRAINT fk_7a099e1a9395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE customer_barbecue ADD CONSTRAINT fk_7a099e1ae2a5d7d4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accessoire_customer ADD CONSTRAINT fk_cd6b1e5ad23b67ed FOREIGN KEY (accessoire_id) REFERENCES accessoire (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE accessoire_customer ADD CONSTRAINT fk_cd6b1e5a9395c3f3 FOREIGN KEY (customer_id) REFERENCES customer (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('DROP TABLE "order"');
        $this->addSql('DROP TABLE order_accessoire');
    }
}
