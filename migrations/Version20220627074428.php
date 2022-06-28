<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220627074428 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration CHANGE payment_name payment_name VARCHAR(255) DEFAULT NULL, CHANGE payment_surname1 payment_surname1 VARCHAR(255) DEFAULT NULL, CHANGE payment_who payment_who INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration CHANGE payment_name payment_name VARCHAR(255) NOT NULL, CHANGE payment_surname1 payment_surname1 VARCHAR(255) NOT NULL, CHANGE payment_who payment_who INT NOT NULL');
    }
}
