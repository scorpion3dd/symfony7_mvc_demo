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

use App\Enum\Environments;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use Doctrine\DBAL\Platforms\PostgreSQLPlatform;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Throwable;

/**
 * Class Version20231103154552
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20231103154552 extends AbstractMigration
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
     * @throws Exception
     */
    public function up(Schema $schema): void
    {
        $this->abortIf(
            ! ($this->platform instanceof MySQLPlatform)
            && ! ($this->platform instanceof PostgreSQLPlatform),
            'Migration can only be executed safely on \'mysql\' or \'postgresql\'.'
        );
        if ($this->platform instanceof MySQLPlatform) {
            $database = $this->connection->getDatabase();
            $this->write('Database = ' . $database);
            $appEnv = getenv('APP_ENV');
            if ($appEnv === Environments::TEST) {
                $file = 'emptyStructureIntegration.sql';
            } else {
                $file = 'emptyStructure.sql';
            }
            $this->write('From file ' . $file);
            $fileSql = __DIR__ . "/sql/$file";
            $this->write('Full path file ' . $fileSql);
            if (! file_exists($fileSql)) {
                $this->abortIf(true, $file . ' - file not exists');
            }
            try {
                /** @var string $sql */
                $sql = file_get_contents($fileSql);
//                $this->write('sql:');
//                $this->write($sql);
                $this->addSql($sql);
            } catch (Throwable $e) {
                $this->write('Version20231103154552 Throwable:');
                $this->write($e->getMessage());
            }
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
        $this->throwIrreversibleMigrationException();
    }
}
