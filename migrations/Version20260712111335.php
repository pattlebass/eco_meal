<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260712111335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE favorite_business (id INT AUTO_INCREMENT NOT NULL, consumer_id_id INT NOT NULL, business_id_id INT NOT NULL, INDEX IDX_A28476C9B429C320 (consumer_id_id), INDEX IDX_A28476C91A579E8 (business_id_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT FK_A28476C9B429C320 FOREIGN KEY (consumer_id_id) REFERENCES consumer (id)');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT FK_A28476C91A579E8 FOREIGN KEY (business_id_id) REFERENCES business (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY FK_A28476C9B429C320');
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY FK_A28476C91A579E8');
        $this->addSql('DROP TABLE favorite_business');
    }
}
