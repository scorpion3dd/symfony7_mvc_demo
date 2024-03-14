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
 * Class Version20231027141903
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20231027141903 extends AbstractMigration
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
            $this->addSql('CREATE TABLE role_permission (id INT AUTO_INCREMENT NOT NULL, permission_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX role_id_permission_id ON role_permission (role_id, permission_id)');
            $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT role_permission_permission_id_fk FOREIGN KEY (permission_id) REFERENCES permission (id)');
            $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT role_permission_role_id_fk FOREIGN KEY (role_id) REFERENCES role (id)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE role_permission (id INT AUTO_INCREMENT NOT NULL, permission_id INT NOT NULL, role_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX role_id_permission_id ON role_permission (role_id, permission_id)');
            $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT role_permission_permission_id_fk FOREIGN KEY (permission_id) REFERENCES permission (id)');
            $this->addSql('ALTER TABLE role_permission ADD CONSTRAINT role_permission_role_id_fk FOREIGN KEY (role_id) REFERENCES role (id)');
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
            $this->addSql('DROP TABLE role_permission');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('DROP TABLE role_permission');
        }
    }
}
