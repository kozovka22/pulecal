<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260126AddAuthAndApiKeys extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add password, roles to User and create API keys table';
    }

    public function up(Schema $schema): void
    {
        // Add password and roles columns to user table if they don't exist
        $table = $schema->getTable('user');
        if (!$table->hasColumn('password')) {
            $this->addSql('ALTER TABLE user ADD password VARCHAR(255) NOT NULL DEFAULT \'\'');
        }
        if (!$table->hasColumn('roles')) {
            $this->addSql('ALTER TABLE user ADD roles JSON NOT NULL DEFAULT \'["ROLE_USER"]\'');
        }

        // Create api_keys table if it doesn't exist
        if (!$schema->hasTable('api_keys')) {
            $this->addSql('CREATE TABLE api_keys (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, `key` VARCHAR(64) UNIQUE NOT NULL, created_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        }
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS api_keys');
        $this->addSql('ALTER TABLE user DROP COLUMN password');
        $this->addSql('ALTER TABLE user DROP COLUMN roles');
    }
}
