<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220609112434 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE registration_extra_field (id INT AUTO_INCREMENT NOT NULL, registration_id INT DEFAULT NULL, extra_field_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_E3E1A65D833D8F43 (registration_id), INDEX IDX_E3E1A65DAEB5FE3 (extra_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE registration_extra_field ADD CONSTRAINT FK_E3E1A65D833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id)');
        $this->addSql('ALTER TABLE registration_extra_field ADD CONSTRAINT FK_E3E1A65DAEB5FE3 FOREIGN KEY (extra_field_id) REFERENCES extra_field (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE registration_extra_field');
    }
}
