<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250124154001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer ADD customer_id INT DEFAULT NULL, ADD user_id INT DEFAULT NULL, DROP name, DROP contact_email, DROP token');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E099395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_81398E099395C3F3 ON customer (customer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_81398E09A76ED395 ON customer (user_id)');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64975A281C7');
        $this->addSql('DROP INDEX IDX_8D93D64975A281C7 ON user');
        $this->addSql('ALTER TABLE user DROP reference_customer_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E099395C3F3');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09A76ED395');
        $this->addSql('DROP INDEX IDX_81398E099395C3F3 ON customer');
        $this->addSql('DROP INDEX UNIQ_81398E09A76ED395 ON customer');
        $this->addSql('ALTER TABLE customer ADD name VARCHAR(255) NOT NULL, ADD contact_email VARCHAR(255) NOT NULL, ADD token JSON NOT NULL, DROP customer_id, DROP user_id');
        $this->addSql('ALTER TABLE user ADD reference_customer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64975A281C7 FOREIGN KEY (reference_customer_id) REFERENCES customer (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_8D93D64975A281C7 ON user (reference_customer_id)');
    }
}
