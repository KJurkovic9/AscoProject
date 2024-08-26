<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240514184651 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE section (id INT AUTO_INCREMENT NOT NULL, guide_id INT NOT NULL, title VARCHAR(255) NOT NULL, text VARCHAR(1024) NOT NULL, image VARCHAR(255) DEFAULT NULL, INDEX IDX_2D737AEFD7ED1D4B (guide_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE section ADD CONSTRAINT FK_2D737AEFD7ED1D4B FOREIGN KEY (guide_id) REFERENCES guide (id)');
        $this->addSql('ALTER TABLE guide DROP text, DROP image');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_CA9EC7352B36786B ON guide (title)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE section DROP FOREIGN KEY FK_2D737AEFD7ED1D4B');
        $this->addSql('DROP TABLE section');
        $this->addSql('DROP INDEX UNIQ_CA9EC7352B36786B ON guide');
        $this->addSql('ALTER TABLE guide ADD text VARCHAR(2048) NOT NULL, ADD image VARCHAR(255) DEFAULT NULL');
    }
}
