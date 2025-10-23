<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251022221341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EA9F8008B6');
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EACFFE9AD6');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EA9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_C82E74E7927C74 ON clients (email)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EACFFE9AD6');
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EA9F8008B6');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EA9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('DROP INDEX UNIQ_C82E74E7927C74 ON clients');
    }
}
