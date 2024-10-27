<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241020094647 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE customers (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(255) NOT NULL, img LONGTEXT NOT NULL, email VARCHAR(255) NOT NULL, phonenumber VARCHAR(255) NOT NULL, status SMALLINT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE genres (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE movies (id INT AUTO_INCREMENT NOT NULL, genre_id_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, decription LONGTEXT NOT NULL, img LONGTEXT NOT NULL, createat DATETIME NOT NULL, INDEX IDX_C61EED30C2428192 (genre_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reviews (id INT AUTO_INCREMENT NOT NULL, movie_id_id INT DEFAULT NULL, customer_id_id INT DEFAULT NULL, reviewtext LONGTEXT NOT NULL, rating INT NOT NULL, INDEX IDX_6970EB0F10684CB (movie_id_id), INDEX IDX_6970EB0FB171EB6C (customer_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE movies ADD CONSTRAINT FK_C61EED30C2428192 FOREIGN KEY (genre_id_id) REFERENCES genres (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F10684CB FOREIGN KEY (movie_id_id) REFERENCES movies (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0FB171EB6C FOREIGN KEY (customer_id_id) REFERENCES customers (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE movies DROP FOREIGN KEY FK_C61EED30C2428192');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F10684CB');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0FB171EB6C');
        $this->addSql('DROP TABLE customers');
        $this->addSql('DROP TABLE genres');
        $this->addSql('DROP TABLE movies');
        $this->addSql('DROP TABLE reviews');
    }
}
