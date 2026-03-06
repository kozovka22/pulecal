<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214213341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CA40A2C8');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C401B253C');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7CA76ED395');
        $this->addSql('ALTER TABLE config DROP FOREIGN KEY FK_D48A2F7C71F7E88B');
        $this->addSql('DROP TABLE config');
        $this->addSql('ALTER TABLE calendar ADD private TINYINT(1) DEFAULT 1 NOT NULL, ADD active TINYINT(1) DEFAULT 1 NOT NULL');
        $this->addSql('ALTER TABLE event_type ADD is_private TINYINT(1) DEFAULT 0 NOT NULL, ADD is_whitelist TINYINT(1) DEFAULT 0 NOT NULL, ADD is_blacklist TINYINT(1) DEFAULT 0 NOT NULL, ADD is_timeslot TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE config (id INT AUTO_INCREMENT NOT NULL, calendar_id INT DEFAULT NULL, user_id INT DEFAULT NULL, event_id INT DEFAULT NULL, event_type_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, value VARCHAR(4096) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_D48A2F7CA76ED395 (user_id), INDEX IDX_D48A2F7C71F7E88B (event_id), INDEX IDX_D48A2F7C401B253C (event_type_id), INDEX IDX_D48A2F7CA40A2C8 (calendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7CA40A2C8 FOREIGN KEY (calendar_id) REFERENCES calendar (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C401B253C FOREIGN KEY (event_type_id) REFERENCES event_type (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE config ADD CONSTRAINT FK_D48A2F7C71F7E88B FOREIGN KEY (event_id) REFERENCES event (id)');
        $this->addSql('ALTER TABLE calendar DROP private, DROP active');
        $this->addSql('ALTER TABLE event_type DROP is_private, DROP is_whitelist, DROP is_blacklist, DROP is_timeslot');
    }
}
