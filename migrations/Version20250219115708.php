<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250219115708 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis ADD id INT AUTO_INCREMENT NOT NULL, DROP id_avis, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE avis MODIFY id INT NOT NULL');
        $this->addSql('DROP INDEX `PRIMARY` ON avis');
        $this->addSql('ALTER TABLE avis ADD id_avis INT NOT NULL, DROP id');
        $this->addSql('ALTER TABLE avis ADD PRIMARY KEY (id_avis)');
    }
}
