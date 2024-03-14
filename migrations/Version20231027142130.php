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
 * Class Version20231027142130
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20231027142130 extends AbstractMigration
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
            $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role_permission_id INT NOT NULL, admin_archived_id INT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX user_id_role_permission_id ON user_role (user_id, role_permission_id)');
            $this->addSql('ALTER TABLE user_role ADD CONSTRAINT user_role_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id)');
            $this->addSql('ALTER TABLE user_role ADD CONSTRAINT user_role_role_permission_id_fk FOREIGN KEY (role_permission_id) REFERENCES role_permission (id)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE user_role (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, role_permission_id INT NOT NULL, admin_archived_id INT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX user_id_role_permission_id ON user_role (user_id, role_permission_id)');
            $this->addSql('ALTER TABLE user_role ADD CONSTRAINT user_role_user_id_fk FOREIGN KEY (user_id) REFERENCES user (id)');
            $this->addSql('ALTER TABLE user_role ADD CONSTRAINT user_role_role_permission_id_fk FOREIGN KEY (role_permission_id) REFERENCES role_permission (id)');
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
            $this->addSql('DROP TABLE user_role');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('DROP TABLE user_role');
        }
    }
}
