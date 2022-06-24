<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220624120306 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration ADD payment_name VARCHAR(255) NOT NULL, ADD payment_surname1 VARCHAR(255) NOT NULL, ADD payment_surname2 VARCHAR(255) DEFAULT NULL, ADD payment_who INT NOT NULL, DROP payer');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration ADD payer INT DEFAULT NULL, DROP payment_name, DROP payment_surname1, DROP payment_surname2, DROP payment_who');
    }
}
