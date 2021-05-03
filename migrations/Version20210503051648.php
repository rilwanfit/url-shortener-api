<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210503051648 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create url table to store generated urls and its parameters';
    }

    public function up(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $sql = <<<EOQ
    CREATE TABLE IF NOT EXISTS url (
    input_url VARCHAR(1024) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`,
    generated_url VARCHAR(128) CHARACTER SET utf8 NOT NULL COLLATE `utf8_unicode_ci`,
    created_at TIMESTAMP,
    unique_hash VARCHAR(64) NOT NULL,
    PRIMARY KEY(unique_hash))
    DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB
EOQ;

        $this->addSql($sql);
    }

    public function down(Schema $schema): void
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE url');
    }
}
