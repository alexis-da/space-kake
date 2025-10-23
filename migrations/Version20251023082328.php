<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251023082328 extends AbstractMigration
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
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EA9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id)');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id)');
        $this->addSql('ALTER TABLE cakes DROP FOREIGN KEY FK_E2EF14312469DE2');
        $this->addSql('ALTER TABLE cakes ADD CONSTRAINT FK_E2EF14312469DE2 FOREIGN KEY (category_id) REFERENCES categories (id)');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE19EB6921');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F19EB6921');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F9F8008B6');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id)');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EACFFE9AD6');
        $this->addSql('ALTER TABLE cake_order DROP FOREIGN KEY FK_89E43EA9F8008B6');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EACFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cake_order ADD CONSTRAINT FK_89E43EA9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id) ON UPDATE NO ACTION ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F9F8008B6');
        $this->addSql('ALTER TABLE reviews DROP FOREIGN KEY FK_6970EB0F19EB6921');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F9F8008B6 FOREIGN KEY (cake_id) REFERENCES cakes (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE reviews ADD CONSTRAINT FK_6970EB0F19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE cakes DROP FOREIGN KEY FK_E2EF14312469DE2');
        $this->addSql('ALTER TABLE cakes ADD CONSTRAINT FK_E2EF14312469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE orders DROP FOREIGN KEY FK_E52FFDEE19EB6921');
        $this->addSql('ALTER TABLE orders ADD CONSTRAINT FK_E52FFDEE19EB6921 FOREIGN KEY (client_id) REFERENCES clients (id) ON UPDATE CASCADE ON DELETE CASCADE');
    }
}
