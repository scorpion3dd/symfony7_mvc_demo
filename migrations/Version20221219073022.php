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
 * Class Version20221219073022
 * Auto-generated Migration: Please modify to your needs!
 * @package DoctrineMigrations
 */
final class Version20221219073022 extends AbstractMigration
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
            $this->addSql('CREATE TABLE comment (id INT NOT NULL AUTO_INCREMENT, user_id INT NOT NULL, author VARCHAR(255) NOT NULL, text TEXT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP, photo_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_9474526C604B8382 ON comment (user_id)');
            $this->addSql('CREATE TABLE messenger_messages (id INT NOT NULL AUTO_INCREMENT, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP, available_at TIMESTAMP, delivered_at DATETIME, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
            $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C604B8382 FOREIGN KEY (user_id) REFERENCES user (id)');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE SEQUENCE comment_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE SEQUENCE user_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
            $this->addSql('CREATE TABLE comment (id INT NOT NULL, user_id INT NOT NULL, author VARCHAR(255) NOT NULL, text TEXT NOT NULL, email VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, photo_filename VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_9474526C604B8382 ON comment (user_id)');
            $this->addSql('COMMENT ON COLUMN comment.created_at IS \'(DC2Type:datetime_immutable)\'');
            $this->addSql('CREATE TABLE messenger_messages (id BIGSERIAL NOT NULL, body TEXT NOT NULL, headers TEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, available_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, delivered_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, PRIMARY KEY(id))');
            $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
            $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
            $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
            $this->addSql('CREATE OR REPLACE FUNCTION notify_messenger_messages() RETURNS TRIGGER AS $$
                                BEGIN
                                    PERFORM pg_notify(\'messenger_messages\', NEW.queue_name::text);
                                    RETURN NEW;
                                END;
                            $$ LANGUAGE plpgsql;');
            $this->addSql('DROP TRIGGER IF EXISTS notify_trigger ON messenger_messages;');
            $this->addSql('CREATE TRIGGER notify_trigger AFTER INSERT OR UPDATE ON messenger_messages FOR EACH ROW EXECUTE PROCEDURE notify_messenger_messages();');
            $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_9474526C604B8382 FOREIGN KEY (user_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
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
            $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C604B8382');
            $this->addSql('DROP TABLE comment');
            $this->addSql('DROP TABLE messenger_messages');
        }
        if ($this->platform instanceof PostgreSQLPlatform) {
            $this->addSql('CREATE SCHEMA public');
            $this->addSql('DROP SEQUENCE comment_id_seq CASCADE');
            $this->addSql('DROP SEQUENCE user_id_seq CASCADE');
            $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_9474526C604B8382');
            $this->addSql('DROP TABLE comment');
            $this->addSql('DROP TABLE messenger_messages');
        }
    }
}
