<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220701090434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP orderd_date');
        $this->addSql('ALTER TABLE customer DROP start_date');
        $this->addSql('ALTER TABLE customer DROP end_date');
        $this->addSql('ALTER TABLE customer DROP price_total');
        $this->addSql('ALTER TABLE customer DROP remark');
        $this->addSql('ALTER TABLE "order" ADD orderd_date DATE NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD end_date DATE NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD price_total INT NOT NULL');
        $this->addSql('ALTER TABLE "order" ADD remark TEXT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE customer ADD orderd_date DATE NOT NULL');
        $this->addSql('ALTER TABLE customer ADD start_date DATE NOT NULL');
        $this->addSql('ALTER TABLE customer ADD end_date DATE NOT NULL');
        $this->addSql('ALTER TABLE customer ADD price_total INT NOT NULL');
        $this->addSql('ALTER TABLE customer ADD remark TEXT NOT NULL');
        $this->addSql('ALTER TABLE "order" DROP orderd_date');
        $this->addSql('ALTER TABLE "order" DROP start_date');
        $this->addSql('ALTER TABLE "order" DROP end_date');
        $this->addSql('ALTER TABLE "order" DROP price_total');
        $this->addSql('ALTER TABLE "order" DROP remark');
    }
}
