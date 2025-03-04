<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250303202102 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prise_medicament ADD patient_id INT NOT NULL, ADD medicament_id INT NOT NULL');
        $this->addSql('ALTER TABLE prise_medicament ADD CONSTRAINT FK_9A13DBE46B899279 FOREIGN KEY (patient_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE prise_medicament ADD CONSTRAINT FK_9A13DBE4AB0D61F7 FOREIGN KEY (medicament_id) REFERENCES medicament (id)');
        $this->addSql('CREATE INDEX IDX_9A13DBE46B899279 ON prise_medicament (patient_id)');
        $this->addSql('CREATE INDEX IDX_9A13DBE4AB0D61F7 ON prise_medicament (medicament_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE prise_medicament DROP FOREIGN KEY FK_9A13DBE46B899279');
        $this->addSql('ALTER TABLE prise_medicament DROP FOREIGN KEY FK_9A13DBE4AB0D61F7');
        $this->addSql('DROP INDEX IDX_9A13DBE46B899279 ON prise_medicament');
        $this->addSql('DROP INDEX IDX_9A13DBE4AB0D61F7 ON prise_medicament');
        $this->addSql('ALTER TABLE prise_medicament DROP patient_id, DROP medicament_id');
    }
}
