<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210724120516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create the table listing the sports';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE sport (
                id INT AUTO_INCREMENT NOT NULL,
                label VARCHAR(60) NOT NULL UNIQUE,
                created_at DATETIME NOT NULL,
                updated_at DATETIME DEFAULT NULL,
                PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE sport');
    }
}
