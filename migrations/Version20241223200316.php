<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241223200316 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Consultation (id_consultation INT AUTO_INCREMENT NOT NULL, id_animal INT DEFAULT NULL, id_user INT DEFAULT NULL, date DATE NOT NULL, nombre_de_consultation INT DEFAULT NULL, health_status VARCHAR(255) NOT NULL, INDEX IDX_8E775E5E4C9C96F2 (id_animal), INDEX IDX_8E775E5E6B3CA4B (id_user), PRIMARY KEY(id_consultation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal (id INT AUTO_INCREMENT NOT NULL, habitat_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, species VARCHAR(255) NOT NULL, birth_date DATE NOT NULL, health_status VARCHAR(255) NOT NULL, feeding_schedule LONGTEXT NOT NULL, description LONGTEXT DEFAULT NULL, image JSON DEFAULT NULL, INDEX IDX_6AAB231FAFFE2D26 (habitat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id INT AUTO_INCREMENT NOT NULL, habitat_id INT NOT NULL, pseudo VARCHAR(255) NOT NULL, avis LONGTEXT NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_8F91ABF0AFFE2D26 (habitat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte_rendu (id INT AUTO_INCREMENT NOT NULL, animal_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, status VARCHAR(50) NOT NULL, INDEX IDX_D39E69D28E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE habitat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, images JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, pseudo VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_8D93D64986CC499D (pseudo), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Consultation ADD CONSTRAINT FK_8E775E5E4C9C96F2 FOREIGN KEY (id_animal) REFERENCES animal (id)');
        $this->addSql('ALTER TABLE Consultation ADD CONSTRAINT FK_8E775E5E6B3CA4B FOREIGN KEY (id_user) REFERENCES user (id)');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0AFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE compte_rendu ADD CONSTRAINT FK_D39E69D28E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Consultation DROP FOREIGN KEY FK_8E775E5E4C9C96F2');
        $this->addSql('ALTER TABLE Consultation DROP FOREIGN KEY FK_8E775E5E6B3CA4B');
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FAFFE2D26');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0AFFE2D26');
        $this->addSql('ALTER TABLE compte_rendu DROP FOREIGN KEY FK_D39E69D28E962C16');
        $this->addSql('DROP TABLE Consultation');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE compte_rendu');
        $this->addSql('DROP TABLE habitat');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
