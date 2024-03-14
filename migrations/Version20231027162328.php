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
 * Class Version20231027162328
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20231027162328 extends AbstractMigration
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
            $this->addSql('CREATE TABLE role_hierarchy (id INT AUTO_INCREMENT NOT NULL, parent_role_id INT NOT NULL, child_role_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX parent_child_role_id ON role_hierarchy (parent_role_id, child_role_id)');
            $this->addSql('ALTER TABLE role_hierarchy ADD CONSTRAINT role_role_child_role_id_fk FOREIGN KEY (child_role_id) REFERENCES role (id)');
            $this->addSql('ALTER TABLE role_hierarchy ADD CONSTRAINT role_role_parent_role_id_fk FOREIGN KEY (parent_role_id) REFERENCES role (id)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE TABLE role_hierarchy (id INT AUTO_INCREMENT NOT NULL, parent_role_id INT NOT NULL, child_role_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
            $this->addSql('CREATE UNIQUE INDEX parent_child_role_id ON role_hierarchy (parent_role_id, child_role_id)');
            $this->addSql('ALTER TABLE role_hierarchy ADD CONSTRAINT role_role_child_role_id_fk FOREIGN KEY (child_role_id) REFERENCES role (id)');
            $this->addSql('ALTER TABLE role_hierarchy ADD CONSTRAINT role_role_parent_role_id_fk FOREIGN KEY (parent_role_id) REFERENCES role (id)');
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
            $this->addSql('DROP TABLE role_hierarchy');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('DROP TABLE role_hierarchy');
        }
    }
}
