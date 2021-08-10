<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210810194304 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE logger ADD server JSON DEFAULT \'{}\' ');
        $this->addSql('ALTER TABLE logger ALTER server SET NOT NULL ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE logger DROP server');
    }
}
