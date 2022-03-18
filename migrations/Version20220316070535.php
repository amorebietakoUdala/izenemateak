<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220316070535 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE clasification (id INT AUTO_INCREMENT NOT NULL, description_es VARCHAR(255) NOT NULL, description_eu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD clasification_id INT NOT NULL, ADD cost DOUBLE PRECISION DEFAULT NULL, ADD deposit DOUBLE PRECISION DEFAULT NULL');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB9DA950E0A FOREIGN KEY (clasification_id) REFERENCES clasification (id)');
        $this->addSql('CREATE INDEX IDX_169E6FB9DA950E0A ON course (clasification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB9DA950E0A');
        $this->addSql('DROP TABLE clasification');
        $this->addSql('DROP INDEX IDX_169E6FB9DA950E0A ON course');
        $this->addSql('ALTER TABLE course DROP clasification_id, DROP cost, DROP deposit');
    }
}
