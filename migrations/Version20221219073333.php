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
 * Class Version20221219073333
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20221219073333 extends AbstractMigration
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
            $this->addSql('CREATE TABLE admin (id INT NOT NULL AUTO_INCREMENT, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76F85E0677 ON admin (username)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE SEQUENCE admin_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE TABLE admin (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE UNIQUE INDEX UNIQ_880E0D76F85E0677 ON admin (username)');
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
            $this->addSql('DROP TABLE admin');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE SCHEMA public');
            $this->addSql('DROP SEQUENCE admin_id_seq CASCADE');
            $this->addSql('DROP TABLE admin');
        }
    }
}
