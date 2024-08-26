<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240421185407 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculation ADD payback_peroid INT NOT NULL, ADD installation_price INT NOT NULL, ADD equipment_price INT NOT NULL, CHANGE profitabilty_monthly profitabilty_monthly JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calculation DROP payback_peroid, DROP installation_price, DROP equipment_price, CHANGE profitabilty_monthly profitabilty_monthly INT NOT NULL');
    }
}
