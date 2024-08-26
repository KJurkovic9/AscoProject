<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240424153508 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, calculation_id INT NOT NULL, user_id INT NOT NULL, name VARCHAR(100) NOT NULL, UNIQUE INDEX UNIQ_2FB3D0EECE3D4B33 (calculation_id), INDEX IDX_2FB3D0EEA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EECE3D4B33 FOREIGN KEY (calculation_id) REFERENCES calculation (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE calculation DROP FOREIGN KEY FK_F6A769707E3C61F9');
        $this->addSql('DROP INDEX IDX_F6A769707E3C61F9 ON calculation');
        $this->addSql('ALTER TABLE calculation DROP owner_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EECE3D4B33');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('DROP TABLE project');
        $this->addSql('ALTER TABLE calculation ADD owner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE calculation ADD CONSTRAINT FK_F6A769707E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_F6A769707E3C61F9 ON calculation (owner_id)');
    }
}
