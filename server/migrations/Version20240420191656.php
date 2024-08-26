<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240420191656 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calculation (id INT AUTO_INCREMENT NOT NULL, owner_id INT DEFAULT NULL, roof_surface DOUBLE PRECISION NOT NULL, roof_pitch INT NOT NULL, roof_orientation VARCHAR(2) NOT NULL, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, lifespan INT DEFAULT NULL, budget INT NOT NULL, project_price INT NOT NULL, profitabilty_years INT NOT NULL, profitabilty_monthly INT NOT NULL, effectiveness INT NOT NULL, INDEX IDX_F6A769707E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calculation ADD CONSTRAINT FK_F6A769707E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE session DROP FOREIGN KEY FK_D044D5D4C00C2939');
        $this->addSql('DROP TABLE pre_calculation');
        $this->addSql('DROP TABLE session');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pre_calculation (id INT AUTO_INCREMENT NOT NULL, roof_surface DOUBLE PRECISION NOT NULL, roof_pitch INT NOT NULL, roof_orientation VARCHAR(2) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, lat DOUBLE PRECISION NOT NULL, lng DOUBLE PRECISION NOT NULL, lifespan INT DEFAULT NULL, budget DOUBLE PRECISION DEFAULT NULL, type_of_counter VARCHAR(13) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, counter_consumption JSON NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE session (id INT AUTO_INCREMENT NOT NULL, user_in_session_id INT NOT NULL, ip_address VARCHAR(50) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, user_agent VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, time_created DATETIME DEFAULT CURRENT_TIMESTAMP NOT NULL, remember_me TINYINT(1) NOT NULL, INDEX IDX_D044D5D4C00C2939 (user_in_session_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE session ADD CONSTRAINT FK_D044D5D4C00C2939 FOREIGN KEY (user_in_session_id) REFERENCES user (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE calculation DROP FOREIGN KEY FK_F6A769707E3C61F9');
        $this->addSql('DROP TABLE calculation');
    }
}
