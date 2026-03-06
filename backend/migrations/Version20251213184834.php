<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251213184834 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(100) NOT NULL, deactivated_at DATETIME DEFAULT NULL, INDEX IDX_6EA9A1467E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendar_event (calendar_id INT NOT NULL, event_id INT NOT NULL, INDEX IDX_57FA09C9A40A2C8 (calendar_id), INDEX IDX_57FA09C971F7E88B (event_id), PRIMARY KEY(calendar_id, event_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE calendar_user (calendar_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_DF05551DA40A2C8 (calendar_id), INDEX IDX_DF05551DA76ED395 (user_id), PRIMARY KEY(calendar_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, calendar_id INT DEFAULT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, event_type_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, value VARCHAR(4096) NOT NULL, INDEX IDX_D48A2F7CA40A2C8 (calendar_id), INDEX IDX_D48A2F7CA76ED395 (user_id), INDEX IDX_D48A2F7C71F7E88B (event_id), INDEX IDX_D48A2F7C401B253C (event_type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event (id INT AUTO_INCREMENT NOT NULL, event_type_id INT DEFAULT NULL, owner_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, datetime DATETIME NOT NULL, repeats TINYINT(1) NOT NULL, repeat_interval VARCHAR(50) DEFAULT NULL, shareable TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_3BAE0AA7401B253C (event_type_id), INDEX IDX_3BAE0AA77E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_shared_users (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7FE420771F7E88B (event_id), INDEX IDX_7FE4207A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE event_type (id INT AUTO_INCREMENT NOT NULL, type_name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, username VARCHAR(100) NOT NULL, email VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_calendar (user_id INT NOT NULL, calendar_id INT NOT NULL, INDEX IDX_8E244546A76ED395 (user_id), INDEX IDX_8E244546A40A2C8 (calendar_id), PRIMARY KEY(user_id, calendar_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A1467E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE calendar_event ADD CONSTRAINT FK_57FA09C9A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_event ADD CONSTRAINT FK_57FA09C971F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_user ADD CONSTRAINT FK_DF05551DA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calendar_user ADD CONSTRAINT FK_DF05551DA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7CA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA7401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE event ADD CONSTRAINT FK_3BAE0AA77E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE event_shared_users ADD CONSTRAINT FK_7FE420771F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_shared_users ADD CONSTRAINT FK_7FE4207A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_calendar ADD CONSTRAINT FK_8E244546A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_calendar ADD CONSTRAINT FK_8E244546A40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A1467E3C61F9');
        $this->addSql('ALTER TABLE calendar_event DROP FOREIGN KEY FK_57FA09C9A40A2C8');
        $this->addSql('ALTER TABLE calendar_event DROP FOREIGN KEY FK_57FA09C971F7E88B');
        $this->addSql('ALTER TABLE calendar_user DROP FOREIGN KEY FK_DF05551DA40A2C8');
        $this->addSql('ALTER TABLE calendar_user DROP FOREIGN KEY FK_DF05551DA76ED395');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CA40A2C8');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CA76ED395');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C71F7E88B');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C401B253C');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA7401B253C');
        $this->addSql('ALTER TABLE event DROP FOREIGN KEY FK_3BAE0AA77E3C61F9');
        $this->addSql('ALTER TABLE event_shared_users DROP FOREIGN KEY FK_7FE420771F7E88B');
        $this->addSql('ALTER TABLE event_shared_users DROP FOREIGN KEY FK_7FE4207A76ED395');
        $this->addSql('ALTER TABLE user_calendar DROP FOREIGN KEY FK_8E244546A76ED395');
        $this->addSql('ALTER TABLE user_calendar DROP FOREIGN KEY FK_8E244546A40A2C8');
        $this->addSql('DROP TABLE calendar');
        $this->addSql('DROP TABLE calendar_event');
        $this->addSql('DROP TABLE calendar_user');
        $this->addSql('DROP TABLE config');
        $this->addSql('DROP TABLE event');
        $this->addSql('DROP TABLE event_shared_users');
        $this->addSql('DROP TABLE event_type');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE user_calendar');
    }
}
