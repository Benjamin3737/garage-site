<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260703150000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add password reset token fields to user entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user ADD reset_password_token VARCHAR(255) DEFAULT NULL, ADD reset_password_token_expires_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP reset_password_token, DROP reset_password_token_expires_at');
    }
}
