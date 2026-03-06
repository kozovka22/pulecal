<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251214213621 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_type ADD private TINYINT(1) DEFAULT 0 NOT NULL, ADD whitelist TINYINT(1) DEFAULT 0 NOT NULL, ADD blacklist TINYINT(1) DEFAULT 0 NOT NULL, ADD timeslot TINYINT(1) DEFAULT 0 NOT NULL, DROP is_private, DROP is_whitelist, DROP is_blacklist, DROP is_timeslot');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE event_type ADD is_private TINYINT(1) DEFAULT 0 NOT NULL, ADD is_whitelist TINYINT(1) DEFAULT 0 NOT NULL, ADD is_blacklist TINYINT(1) DEFAULT 0 NOT NULL, ADD is_timeslot TINYINT(1) DEFAULT 0 NOT NULL, DROP private, DROP whitelist, DROP blacklist, DROP timeslot');
    }
}
