<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214212112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_users (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_559814C571F7E88B (event_id), INDEX IDX_559814C5A76ED395 (user_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C571F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users ADD CONSTRAINT FK_559814C5A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_shared_users DROP FOREIGN KEY FK_7FE420771F7E88B');
        $this->addSql('ALTER TABLE event_shared_users DROP FOREIGN KEY FK_7FE4207A76ED395');
        $this->addSql('DROP TABLE event_shared_users');
        $this->addSql('ALTER TABLE event ADD private TINYINT(1) DEFAULT 1 NOT NULL, DROP shareable');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE event_shared_users (event_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_7FE4207A76ED395 (user_id), INDEX IDX_7FE420771F7E88B (event_id), PRIMARY KEY(event_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE event_shared_users ADD CONSTRAINT FK_7FE420771F7E88B FOREIGN KEY (event_id) REFERENCES event (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_shared_users ADD CONSTRAINT FK_7FE4207A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C571F7E88B');
        $this->addSql('ALTER TABLE event_users DROP FOREIGN KEY FK_559814C5A76ED395');
        $this->addSql('DROP TABLE event_users');
        $this->addSql('ALTER TABLE event ADD shareable TINYINT(1) DEFAULT 0 NOT NULL, DROP private');
    }
}
