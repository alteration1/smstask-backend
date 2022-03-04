<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220303215331 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE attempts (id INT AUTO_INCREMENT NOT NULL, attempt DATETIME NOT NULL, success TINYINT(1) DEFAULT NULL, codeId INT DEFAULT NULL, INDEX IDX_BFC7E764B5FC0459 (codeId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE codes (id INT AUTO_INCREMENT NOT NULL, phone VARCHAR(255) NOT NULL, code VARCHAR(255) NOT NULL, send_at DATETIME NOT NULL, valid TINYINT(1) DEFAULT NULL, success TINYINT(1) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE `user` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, created DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE attempts ADD CONSTRAINT FK_BFC7E764B5FC0459 FOREIGN KEY (codeId) REFERENCES codes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE attempts DROP FOREIGN KEY FK_BFC7E764B5FC0459');
        $this->addSql('DROP TABLE attempts');
        $this->addSql('DROP TABLE codes');
        $this->addSql('DROP TABLE `user`');
    }
}
