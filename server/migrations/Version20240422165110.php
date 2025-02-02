<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240422165110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(40) NOT NULL, postal_code VARCHAR(20) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile DROP INDEX IDX_D95AB405A76ED395, ADD UNIQUE INDEX UNIQ_D95AB405A76ED395 (user_id)');
        $this->addSql('ALTER TABLE user_profile ADD city_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4058BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_D95AB4058BAC62AF ON user_profile (city_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB4058BAC62AF');
        $this->addSql('DROP TABLE city');
        $this->addSql('ALTER TABLE user_profile DROP INDEX UNIQ_D95AB405A76ED395, ADD INDEX IDX_D95AB405A76ED395 (user_id)');
        $this->addSql('DROP INDEX UNIQ_D95AB4058BAC62AF ON user_profile');
        $this->addSql('ALTER TABLE user_profile DROP city_id');
    }
}
