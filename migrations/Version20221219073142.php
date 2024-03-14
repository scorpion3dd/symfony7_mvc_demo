<?php
/**
 * This file is part of the Simple Web Demo Free Lottery Management Application.
 *
 * This project is no longer maintained.
 * The project is written in Symfony Framework Release.
 *
 * @link https://github.com/scorpion3dd
 * @author Denis Puzik <scorpion3dd@gmail.com>
 * @copyright Copyright (c) 2023-2024 scorpion3dd
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Class Version20221219073142
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20221219073142 extends AbstractMigration
{
    /**
     * @return string
     */
    public function getDescription(): string
    {
        return '';
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            ! ($this->platform instanceof MySQLPlatform)
            && ! ($this->platform instanceof PostgreSQLPlatform),
            'Migration can only be executed safely on \'mysql\' or \'postgresql\'.'
        );
        if ($this->platform instanceof MySQLPlatform) {
            $this->addSql('
            CREATE TABLE sessions (
                sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
                sess_data TEXT NOT NULL,
                sess_lifetime INTEGER NOT NULL,
                sess_time INTEGER NOT NULL
            )
        ');
            $this->addSql('CREATE INDEX expiry ON sessions (sess_lifetime)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE sessions (
                    sess_id VARCHAR(128) NOT NULL PRIMARY KEY,
                    sess_data BYTEA NOT NULL,
                    sess_lifetime INTEGER NOT NULL,
                    sess_time INTEGER NOT NULL
                )
            ');
            $this->addSql('CREATE INDEX expiry ON sessions (sess_lifetime)');
        }
    }

    /**
     * @param Schema $schema
     *
     * @return void
     */
    public function down(Schema $schema): void
    {
        $this->abortIf(
            ! ($this->platform instanceof MySQLPlatform)
            && ! ($this->platform instanceof PostgreSQLPlatform),
            'Migration can only be executed safely on \'mysql\' or \'postgresql\'.'
        );
        if ($this->platform instanceof MySQLPlatform) {
            $this->addSql('CREATE SCHEMA public');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE SCHEMA public');
        }
    }
}
