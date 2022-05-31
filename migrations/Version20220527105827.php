<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220527105827 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, activity_type_id INT NOT NULL, clasification_id INT NOT NULL, name_es VARCHAR(255) NOT NULL, name_eu VARCHAR(255) NOT NULL, turn_es VARCHAR(255) NOT NULL, turn_eu VARCHAR(255) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, active TINYINT(1) DEFAULT NULL, places INT DEFAULT NULL, limit_places TINYINT(1) DEFAULT NULL, status INT DEFAULT NULL, cost DOUBLE PRECISION DEFAULT NULL, deposit DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AC74095AC51EFA73 (activity_type_id), INDEX IDX_AC74095ADA950E0A (clasification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clasification (id INT AUTO_INCREMENT NOT NULL, description_es VARCHAR(255) NOT NULL, description_eu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, email VARCHAR(255) NOT NULL, dni VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, surname1 VARCHAR(30) NOT NULL, surname2 VARCHAR(30) DEFAULT NULL, telephone1 VARCHAR(255) NOT NULL, telephone2 VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, subscriber TINYINT(1) NOT NULL, representative_dni VARCHAR(255) DEFAULT NULL, representative_name VARCHAR(255) DEFAULT NULL, representative_surname1 VARCHAR(30) DEFAULT NULL, representative_surname2 VARCHAR(30) DEFAULT NULL, for_me TINYINT(1) NOT NULL, payment_dni VARCHAR(10) NOT NULL, payment_ibanaccount VARCHAR(29) NOT NULL, fortunate TINYINT(1) DEFAULT NULL, confirmed TINYINT(1) DEFAULT NULL, token VARCHAR(50) DEFAULT NULL, confirmation_date DATETIME DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_62A8A7A781C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, activated TINYINT(1) DEFAULT 1, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ADA950E0A FOREIGN KEY (clasification_id) REFERENCES clasification (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A781C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A781C06096');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC51EFA73');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095ADA950E0A');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE clasification');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE user');
    }
}
