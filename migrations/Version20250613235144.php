<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250613235144 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE point (id SERIAL NOT NULL, zone_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, nb_habitants INT NOT NULL, nb_symptomatiques INT NOT NULL, nb_positifs INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_B7A5F3249F2C3FAB ON point (zone_id)');
        $this->addSql('CREATE TABLE zone (id SERIAL NOT NULL, pays_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_A0EBC007A6E44244 ON zone (pays_id)');
        $this->addSql('ALTER TABLE point ADD CONSTRAINT FK_B7A5F3249F2C3FAB FOREIGN KEY (zone_id) REFERENCES zone (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE zone ADD CONSTRAINT FK_A0EBC007A6E44244 FOREIGN KEY (pays_id) REFERENCES pays (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE point DROP CONSTRAINT FK_B7A5F3249F2C3FAB');
        $this->addSql('ALTER TABLE zone DROP CONSTRAINT FK_A0EBC007A6E44244');
        $this->addSql('DROP TABLE point');
        $this->addSql('DROP TABLE zone');
    }
}
