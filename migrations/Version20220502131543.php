<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220502131543 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE answer (id INT AUTO_INCREMENT NOT NULL, demand_id INT NOT NULL, user_id INT NOT NULL, answer_date DATETIME NOT NULL, comments VARCHAR(255) NOT NULL, INDEX IDX_DADD4A255D022E59 (demand_id), INDEX IDX_DADD4A25A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE availablity (id INT AUTO_INCREMENT NOT NULL, day_id INT NOT NULL, begin_time TIME NOT NULL, end_time TIME NOT NULL, INDEX IDX_BD0630819C24126 (day_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE complaint (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, admin_id INT NOT NULL, status_id INT NOT NULL, complaint_date DATE NOT NULL, description VARCHAR(255) NOT NULL, INDEX IDX_5F2732B5A76ED395 (user_id), INDEX IDX_5F2732B5642B8210 (admin_id), INDEX IDX_5F2732B56BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demand (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, status_id INT NOT NULL, user_id INT NOT NULL, title VARCHAR(255) NOT NULL, subject VARCHAR(255) NOT NULL, publish_date DATETIME NOT NULL, comments VARCHAR(255) NOT NULL, INDEX IDX_428D797312469DE2 (category_id), INDEX IDX_428D79736BF700BD (status_id), INDEX IDX_428D7973A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE meeting (id INT AUTO_INCREMENT NOT NULL, student_id INT NOT NULL, tutor_id INT NOT NULL, status_id INT NOT NULL, motive VARCHAR(255) NOT NULL, date DATE NOT NULL, meeting_time TIME NOT NULL, location VARCHAR(255) NOT NULL, comments VARCHAR(255) DEFAULT NULL, INDEX IDX_F515E139CB944F1A (student_id), INDEX IDX_F515E139208F64F1 (tutor_id), INDEX IDX_F515E1396BF700BD (status_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE role (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status (id INT AUTO_INCREMENT NOT NULL, status_type_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, INDEX IDX_7B00651CCD9CFB16 (status_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE status_type (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, role_id INT NOT NULL, mastered_subject_id INT DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, institution VARCHAR(255) DEFAULT NULL, field VARCHAR(255) DEFAULT NULL, phone BIGINT DEFAULT NULL, birthdate DATE NOT NULL, image LONGBLOB DEFAULT NULL, valid_account TINYINT(1) NOT NULL, registered_date DATE NOT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649D60322AC (role_id), INDEX IDX_8D93D649FA8DF81D (mastered_subject_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_availablity (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, availablity_id INT NOT NULL, INDEX IDX_141DF914A76ED395 (user_id), INDEX IDX_141DF914B09327FC (availablity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE week_day (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A255D022E59 FOREIGN KEY (demand_id) REFERENCES demand (id)');
        $this->addSql('ALTER TABLE answer ADD CONSTRAINT FK_DADD4A25A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE availablity ADD CONSTRAINT FK_BD0630819C24126 FOREIGN KEY (day_id) REFERENCES week_day (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B5642B8210 FOREIGN KEY (admin_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE complaint ADD CONSTRAINT FK_5F2732B56BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE demand ADD CONSTRAINT FK_428D797312469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE demand ADD CONSTRAINT FK_428D79736BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE demand ADD CONSTRAINT FK_428D7973A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139CB944F1A FOREIGN KEY (student_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E139208F64F1 FOREIGN KEY (tutor_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE meeting ADD CONSTRAINT FK_F515E1396BF700BD FOREIGN KEY (status_id) REFERENCES status (id)');
        $this->addSql('ALTER TABLE status ADD CONSTRAINT FK_7B00651CCD9CFB16 FOREIGN KEY (status_type_id) REFERENCES status_type (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649D60322AC FOREIGN KEY (role_id) REFERENCES role (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649FA8DF81D FOREIGN KEY (mastered_subject_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE user_availablity ADD CONSTRAINT FK_141DF914A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_availablity ADD CONSTRAINT FK_141DF914B09327FC FOREIGN KEY (availablity_id) REFERENCES availablity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_availablity DROP FOREIGN KEY FK_141DF914B09327FC');
        $this->addSql('ALTER TABLE demand DROP FOREIGN KEY FK_428D797312469DE2');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649FA8DF81D');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A255D022E59');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649D60322AC');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B56BF700BD');
        $this->addSql('ALTER TABLE demand DROP FOREIGN KEY FK_428D79736BF700BD');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E1396BF700BD');
        $this->addSql('ALTER TABLE status DROP FOREIGN KEY FK_7B00651CCD9CFB16');
        $this->addSql('ALTER TABLE answer DROP FOREIGN KEY FK_DADD4A25A76ED395');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5A76ED395');
        $this->addSql('ALTER TABLE complaint DROP FOREIGN KEY FK_5F2732B5642B8210');
        $this->addSql('ALTER TABLE demand DROP FOREIGN KEY FK_428D7973A76ED395');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139CB944F1A');
        $this->addSql('ALTER TABLE meeting DROP FOREIGN KEY FK_F515E139208F64F1');
        $this->addSql('ALTER TABLE user_availablity DROP FOREIGN KEY FK_141DF914A76ED395');
        $this->addSql('ALTER TABLE availablity DROP FOREIGN KEY FK_BD0630819C24126');
        $this->addSql('DROP TABLE answer');
        $this->addSql('DROP TABLE availablity');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE complaint');
        $this->addSql('DROP TABLE demand');
        $this->addSql('DROP TABLE meeting');
        $this->addSql('DROP TABLE role');
        $this->addSql('DROP TABLE status');
        $this->addSql('DROP TABLE status_type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_availablity');
        $this->addSql('DROP TABLE week_day');
    }
}
