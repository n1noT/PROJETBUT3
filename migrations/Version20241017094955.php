<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241017094955 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_events (user_id INT NOT NULL, events_id INT NOT NULL, INDEX IDX_36D54C77A76ED395 (user_id), INDEX IDX_36D54C779D6A1065 (events_id), PRIMARY KEY(user_id, events_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_events ADD CONSTRAINT FK_36D54C77A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_events ADD CONSTRAINT FK_36D54C779D6A1065 FOREIGN KEY (events_id) REFERENCES events (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_events DROP FOREIGN KEY FK_36D54C77A76ED395');
        $this->addSql('ALTER TABLE user_events DROP FOREIGN KEY FK_36D54C779D6A1065');
        $this->addSql('DROP TABLE user_events');
    }
}
