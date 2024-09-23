<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240920090538 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE User (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, mot_de_passe VARCHAR(255) NOT NULL, roles JSON NOT NULL, UNIQUE INDEX UNIQ_2DA17977E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE animal (id_animal INT AUTO_INCREMENT NOT NULL, habitat_id INT NOT NULL, prenom VARCHAR(50) NOT NULL, race VARCHAR(50) NOT NULL, etat VARCHAR(50) DEFAULT NULL, INDEX IDX_6AAB231FAFFE2D26 (habitat_id), PRIMARY KEY(id_animal)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE avis (id_avis INT AUTO_INCREMENT NOT NULL, id_habitat INT DEFAULT NULL, pseudo VARCHAR(255) NOT NULL, avis LONGTEXT NOT NULL, statut TINYINT(1) NOT NULL, INDEX IDX_8F91ABF0E5AA1B98 (id_habitat), PRIMARY KEY(id_avis)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE compte_rendu (id INT AUTO_INCREMENT NOT NULL, animal_id INT DEFAULT NULL, description VARCHAR(255) NOT NULL, date DATETIME NOT NULL, INDEX IDX_D39E69D28E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE consultation (id_consultation INT AUTO_INCREMENT NOT NULL, id_habitat INT DEFAULT NULL, animal_id INT DEFAULT NULL, date DATE NOT NULL, nombre_de_consultation INT DEFAULT NULL, INDEX IDX_964685A6E5AA1B98 (id_habitat), INDEX IDX_964685A68E962C16 (animal_id), PRIMARY KEY(id_consultation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE employee (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, phone VARCHAR(15) DEFAULT NULL, INDEX IDX_5D9F75A1ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE habitat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, images JSON DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE image (id INT AUTO_INCREMENT NOT NULL, animal_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, INDEX IDX_C53D045F8E962C16 (animal_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE animal ADD CONSTRAINT FK_6AAB231FAFFE2D26 FOREIGN KEY (habitat_id) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE avis ADD CONSTRAINT FK_8F91ABF0E5AA1B98 FOREIGN KEY (id_habitat) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE compte_rendu ADD CONSTRAINT FK_D39E69D28E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id_Animal)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A6E5AA1B98 FOREIGN KEY (id_habitat) REFERENCES habitat (id)');
        $this->addSql('ALTER TABLE consultation ADD CONSTRAINT FK_964685A68E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id_Animal)');
        $this->addSql('ALTER TABLE employee ADD CONSTRAINT FK_5D9F75A1ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE image ADD CONSTRAINT FK_C53D045F8E962C16 FOREIGN KEY (animal_id) REFERENCES animal (id_Animal)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE animal DROP FOREIGN KEY FK_6AAB231FAFFE2D26');
        $this->addSql('ALTER TABLE avis DROP FOREIGN KEY FK_8F91ABF0E5AA1B98');
        $this->addSql('ALTER TABLE compte_rendu DROP FOREIGN KEY FK_D39E69D28E962C16');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A6E5AA1B98');
        $this->addSql('ALTER TABLE consultation DROP FOREIGN KEY FK_964685A68E962C16');
        $this->addSql('ALTER TABLE employee DROP FOREIGN KEY FK_5D9F75A1ED5CA9E6');
        $this->addSql('ALTER TABLE image DROP FOREIGN KEY FK_C53D045F8E962C16');
        $this->addSql('DROP TABLE User');
        $this->addSql('DROP TABLE animal');
        $this->addSql('DROP TABLE avis');
        $this->addSql('DROP TABLE compte_rendu');
        $this->addSql('DROP TABLE consultation');
        $this->addSql('DROP TABLE employee');
        $this->addSql('DROP TABLE habitat');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
