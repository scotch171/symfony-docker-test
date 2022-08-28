<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220827094213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add machine table';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('
CREATE TABLE machine (
    id INT AUTO_INCREMENT NOT NULL,
    cpu INT NOT NULL,
    memory INT NOT NULL,
    PRIMARY KEY(id)
                     ) 
DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE machine');
    }
}
