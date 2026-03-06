<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260126AddUserAuthFields extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add password and roles fields to User entity for authentication';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL DEFAULT \'\'');
        $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL DEFAULT \'["ROLE_USER"]\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP COLUMN password');
        $this->addSql('ALTER TABLE user DROP COLUMN roles');
    }
}
