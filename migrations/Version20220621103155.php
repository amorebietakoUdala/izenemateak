<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220621103155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity_extra_field (activity_id INT NOT NULL, extra_field_id INT NOT NULL, INDEX IDX_AFE06FF481C06096 (activity_id), INDEX IDX_AFE06FF4AEB5FE3 (extra_field_id), PRIMARY KEY(activity_id, extra_field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity_extra_field ADD CONSTRAINT FK_AFE06FF481C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_extra_field ADD CONSTRAINT FK_AFE06FF4AEB5FE3 FOREIGN KEY (extra_field_id) REFERENCES extra_field (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE activity_extra_field');
    }
}
