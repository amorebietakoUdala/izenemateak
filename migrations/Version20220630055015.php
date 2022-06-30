<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220630055015 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE activity (id INT AUTO_INCREMENT NOT NULL, activity_type_id INT NOT NULL, clasification_id INT NOT NULL, name_es VARCHAR(255) NOT NULL, name_eu VARCHAR(255) NOT NULL, turn_es VARCHAR(255) NOT NULL, turn_eu VARCHAR(255) NOT NULL, start_date DATE DEFAULT NULL, end_date DATE DEFAULT NULL, active TINYINT(1) DEFAULT NULL, places INT DEFAULT NULL, limit_places TINYINT(1) DEFAULT NULL, status INT DEFAULT NULL, cost DOUBLE PRECISION DEFAULT NULL, cost_for_subscribers DOUBLE PRECISION DEFAULT NULL, accounting_concept VARCHAR(10) NOT NULL, domiciled TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_AC74095AC51EFA73 (activity_type_id), INDEX IDX_AC74095ADA950E0A (clasification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_extra_field (activity_id INT NOT NULL, extra_field_id INT NOT NULL, INDEX IDX_AFE06FF481C06096 (activity_id), INDEX IDX_AFE06FF4AEB5FE3 (extra_field_id), PRIMARY KEY(activity_id, extra_field_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE activity_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE clasification (id INT AUTO_INCREMENT NOT NULL, description_es VARCHAR(255) NOT NULL, description_eu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE extra_field (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, name_eu VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration (id INT AUTO_INCREMENT NOT NULL, activity_id INT NOT NULL, user_id INT DEFAULT NULL, for_me TINYINT(1) NOT NULL, dni VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, name VARCHAR(255) NOT NULL, surname1 VARCHAR(30) NOT NULL, surname2 VARCHAR(30) DEFAULT NULL, telephone1 VARCHAR(255) NOT NULL, telephone2 VARCHAR(255) DEFAULT NULL, date_of_birth DATE DEFAULT NULL, subscriber TINYINT(1) NOT NULL, representative_dni VARCHAR(255) DEFAULT NULL, representative_name VARCHAR(255) DEFAULT NULL, representative_surname1 VARCHAR(30) DEFAULT NULL, representative_surname2 VARCHAR(30) DEFAULT NULL, fortunate TINYINT(1) DEFAULT NULL, confirmed TINYINT(1) DEFAULT NULL, token VARCHAR(50) DEFAULT NULL, confirmation_date DATETIME DEFAULT NULL, payment_who INT DEFAULT NULL, payment_dni VARCHAR(10) DEFAULT NULL, payment_name VARCHAR(255) DEFAULT NULL, payment_surname1 VARCHAR(255) DEFAULT NULL, payment_surname2 VARCHAR(255) DEFAULT NULL, payment_ibanaccount VARCHAR(29) DEFAULT NULL, payment_url VARCHAR(1024) DEFAULT NULL, waiting_list_order INT DEFAULT NULL, called_on_waiting_list TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_62A8A7A781C06096 (activity_id), INDEX IDX_62A8A7A7A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE registration_extra_field (id INT AUTO_INCREMENT NOT NULL, registration_id INT DEFAULT NULL, extra_field_id INT DEFAULT NULL, value VARCHAR(255) DEFAULT NULL, INDEX IDX_E3E1A65D833D8F43 (registration_id), INDEX IDX_E3E1A65DAEB5FE3 (extra_field_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, activated TINYINT(1) DEFAULT 1, last_login DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649F85E0677 (username), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095AC51EFA73 FOREIGN KEY (activity_type_id) REFERENCES activity_type (id)');
        $this->addSql('ALTER TABLE activity ADD CONSTRAINT FK_AC74095ADA950E0A FOREIGN KEY (clasification_id) REFERENCES clasification (id)');
        $this->addSql('ALTER TABLE activity_extra_field ADD CONSTRAINT FK_AFE06FF481C06096 FOREIGN KEY (activity_id) REFERENCES activity (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE activity_extra_field ADD CONSTRAINT FK_AFE06FF4AEB5FE3 FOREIGN KEY (extra_field_id) REFERENCES extra_field (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A781C06096 FOREIGN KEY (activity_id) REFERENCES activity (id)');
        $this->addSql('ALTER TABLE registration ADD CONSTRAINT FK_62A8A7A7A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE registration_extra_field ADD CONSTRAINT FK_E3E1A65D833D8F43 FOREIGN KEY (registration_id) REFERENCES registration (id)');
        $this->addSql('ALTER TABLE registration_extra_field ADD CONSTRAINT FK_E3E1A65DAEB5FE3 FOREIGN KEY (extra_field_id) REFERENCES extra_field (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE activity_extra_field DROP FOREIGN KEY FK_AFE06FF481C06096');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A781C06096');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095AC51EFA73');
        $this->addSql('ALTER TABLE activity DROP FOREIGN KEY FK_AC74095ADA950E0A');
        $this->addSql('ALTER TABLE activity_extra_field DROP FOREIGN KEY FK_AFE06FF4AEB5FE3');
        $this->addSql('ALTER TABLE registration_extra_field DROP FOREIGN KEY FK_E3E1A65DAEB5FE3');
        $this->addSql('ALTER TABLE registration_extra_field DROP FOREIGN KEY FK_E3E1A65D833D8F43');
        $this->addSql('ALTER TABLE registration DROP FOREIGN KEY FK_62A8A7A7A76ED395');
        $this->addSql('DROP TABLE activity');
        $this->addSql('DROP TABLE activity_extra_field');
        $this->addSql('DROP TABLE activity_type');
        $this->addSql('DROP TABLE clasification');
        $this->addSql('DROP TABLE extra_field');
        $this->addSql('DROP TABLE registration');
        $this->addSql('DROP TABLE registration_extra_field');
        $this->addSql('DROP TABLE user');
    }
}
