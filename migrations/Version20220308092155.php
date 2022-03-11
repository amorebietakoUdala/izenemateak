<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220308092155 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, name_es VARCHAR(255) NOT NULL, name_eu VARCHAR(255) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, active TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_169E6FB981C06096 (activity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE course_session (course_id INT NOT NULL, session_id INT NOT NULL, INDEX IDX_D887D038591CC992 (course_id), INDEX IDX_D887D038613FECDF (session_id), PRIMARY KEY(course_id, session_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, course_id INT NOT NULL, session_id INT DEFAULT NULL, email VARCHAR(255) NOT NULL, dni VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, surname1 VARCHAR(30) NOT NULL, surname2 VARCHAR(30) DEFAULT NULL, telephone1 VARCHAR(255) NOT NULL, telephone2 VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, subscriber TINYINT(1) NOT NULL, representative_dni VARCHAR(255) DEFAULT NULL, representative_name VARCHAR(255) DEFAULT NULL, representative_surname1 VARCHAR(30) NOT NULL, representative_surname2 VARCHAR(30) DEFAULT NULL, for_me TINYINT(1) NOT NULL, payment_dni VARCHAR(10) DEFAULT NULL, payment_ibanaccount VARCHAR(29) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_62A8A7A7591CC992 (course_id), INDEX IDX_62A8A7A7613FECDF (session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, description_es VARCHAR(255) NOT NULL, description_eu VARCHAR(255) NOT NULL, active TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, activated TINYINT(1) DEFAULT 1, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE course ADD CONSTRAINT FK_169E6FB981C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D038591CC992 FOREIGN KEY (course_id) REFERENCES course (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE course_session ADD CONSTRAINT FK_D887D038613FECDF FOREIGN KEY (session_id) REFERENCES session (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7591CC992 FOREIGN KEY (course_id) REFERENCES course (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7613FECDF FOREIGN KEY (session_id) REFERENCES session (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE course DROP FOREIGN KEY FK_169E6FB981C06096');
        $this->addSql('ALTER TABLE course_session DROP FOREIGN KEY FK_D887D038591CC992');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7591CC992');
        $this->addSql('ALTER TABLE course_session DROP FOREIGN KEY FK_D887D038613FECDF');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7613FECDF');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE course');
        $this->addSql('DROP TABLE course_session');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE session');
        $this->addSql('DROP TABLE user');
    }
}
