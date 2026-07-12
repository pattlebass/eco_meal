<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260712111802 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY `FK_A28476C91A579E8`');
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY `FK_A28476C9B429C320`');
        $this->addSql('DROP INDEX IDX_A28476C91A579E8 ON favorite_business');
        $this->addSql('DROP INDEX IDX_A28476C9B429C320 ON favorite_business');
        $this->addSql('ALTER TABLE favorite_business ADD consumer_id INT NOT NULL, ADD business_id INT NOT NULL, DROP consumer_id_id, DROP business_id_id');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT FK_A28476C937FDBD6D FOREIGN KEY (consumer_id) REFERENCES consumer (id)');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT FK_A28476C9A89DB457 FOREIGN KEY (business_id) REFERENCES business (id)');
        $this->addSql('CREATE INDEX IDX_A28476C937FDBD6D ON favorite_business (consumer_id)');
        $this->addSql('CREATE INDEX IDX_A28476C9A89DB457 ON favorite_business (business_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY FK_A28476C937FDBD6D');
        $this->addSql('ALTER TABLE favorite_business DROP FOREIGN KEY FK_A28476C9A89DB457');
        $this->addSql('DROP INDEX IDX_A28476C937FDBD6D ON favorite_business');
        $this->addSql('DROP INDEX IDX_A28476C9A89DB457 ON favorite_business');
        $this->addSql('ALTER TABLE favorite_business ADD consumer_id_id INT NOT NULL, ADD business_id_id INT NOT NULL, DROP consumer_id, DROP business_id');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT `FK_A28476C91A579E8` FOREIGN KEY (business_id_id) REFERENCES business (id)');
        $this->addSql('ALTER TABLE favorite_business ADD CONSTRAINT `FK_A28476C9B429C320` FOREIGN KEY (consumer_id_id) REFERENCES consumer (id)');
        $this->addSql('CREATE INDEX IDX_A28476C91A579E8 ON favorite_business (business_id_id)');
        $this->addSql('CREATE INDEX IDX_A28476C9B429C320 ON favorite_business (consumer_id_id)');
    }
}
