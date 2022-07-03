<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220703122631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE order_barbecue (order_id INT NOT NULL, barbecue_id INT NOT NULL, PRIMARY KEY(order_id, barbecue_id))');
        $this->addSql('CREATE INDEX IDX_B7343F848D9F6D38 ON order_barbecue (order_id)');
        $this->addSql('CREATE INDEX IDX_B7343F84E2A5D7D4 ON order_barbecue (barbecue_id)');
        $this->addSql('ALTER TABLE order_barbecue ADD CONSTRAINT FK_B7343F848D9F6D38 FOREIGN KEY (order_id) REFERENCES "order" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE order_barbecue ADD CONSTRAINT FK_B7343F84E2A5D7D4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE "order" DROP CONSTRAINT fk_f5299398e2a5d7d4');
        $this->addSql('DROP INDEX idx_f5299398e2a5d7d4');
        $this->addSql('ALTER TABLE "order" DROP barbecue_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP TABLE order_barbecue');
        $this->addSql('ALTER TABLE "order" ADD barbecue_id INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD CONSTRAINT fk_f5299398e2a5d7d4 FOREIGN KEY (barbecue_id) REFERENCES barbecue (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_f5299398e2a5d7d4 ON "order" (barbecue_id)');
    }
}
