#  This file is part of the Simple Web Demo Free Lottery Management Application.
#
#  This project is no longer maintained.
#  The project is written in Symfony Framework Release.
#
#  @link https://github.com/scorpion3dd
#  @author Denis Puzik <scorpion3dd@gmail.com>
#  @copyright Copyright (c) 2023-2024 scorpion3dd

SET NAMES 'utf8';
USE `learn`;



-- -----------------------------------------------------
-- table logs
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `logs` (
    `id` int NOT NULL AUTO_INCREMENT,
    `ts` TIMESTAMP DEFAULT current_timestamp,
    `user_id` INT(11) DEFAULT NULL,
    `msg` VARCHAR(2048),
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- procedure logWrite
-- -----------------------------------------------------
/*
CALL `logWrite`('trigger user_AFTER_INSERT begin');
CALL `logWrite`(concat_ws(': ','@simple_id',  @simple_id));
*/
DROP PROCEDURE IF EXISTS `logWrite`;
CREATE PROCEDURE `logWrite`(
    IN logMsg NVARCHAR(2048)
)
BEGIN
    INSERT INTO `logs` (`msg`, `user_id`) VALUES (logMsg, @SESSION.user_id);
END;


-- -----------------------------------------------------
-- procedure logReset
-- -----------------------------------------------------
/*
CALL `logReset`();
*/
DROP PROCEDURE IF EXISTS `logReset`;
CREATE PROCEDURE `logReset`()
BEGIN
    CREATE TABLE IF NOT EXISTS `logs` (
        `id` int NOT NULL AUTO_INCREMENT,
        `ts` TIMESTAMP DEFAULT current_timestamp,
        `user_id` INT(11) DEFAULT NULL,
        `msg` VARCHAR(2048),
        PRIMARY KEY (`id`)
    )
        ENGINE = INNODB,
        AUTO_INCREMENT = 2,
        AVG_ROW_LENGTH = 16384,
        CHARACTER SET utf8mb4,
        COLLATE utf8mb4_unicode_ci;

    TRUNCATE TABLE `logs`;
END;


-- -----------------------------------------------------
-- trigger user_AFTER_INSERT
-- -----------------------------------------------------
CREATE
    TRIGGER `user_AFTER_INSERT`
    AFTER INSERT ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_AFTER_INSERT';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_log`
    (`user_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (NEW.`id`,
            v_user_id,
            1,
            '',
            NOW());
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- trigger user_AFTER_UPDATE
-- -----------------------------------------------------
CREATE
    TRIGGER `user_AFTER_UPDATE`
    AFTER UPDATE ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_AFTER_UPDATE';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (NEW.`email` <> OLD.`email` or
        NEW.`full_name` <> OLD.`full_name` or
        NEW.`description` <> OLD.`description` or
        NEW.`status` <> OLD.`status` or
        NEW.`access` <> OLD.`access` or
        NEW.`gender` <> OLD.`gender` or
        NEW.`date_birthday` <> OLD.`date_birthday` or
        NEW.`created_at` <> OLD.`created_at`
        )
    THEN
        IF (NEW.`email` <> OLD.`email`) THEN
            SET v_changed = CONCAT(v_changed, 'email = ', NEW.`email`, '; ');
        END IF;
        IF (NEW.`full_name` <> OLD.`full_name`) THEN
            SET v_changed = CONCAT(v_changed, 'full_name = ', NEW.`full_name`, '; ');
        END IF;
        IF (NEW.`description` <> OLD.`description`) THEN
            SET v_changed = CONCAT(v_changed, 'description = ', NEW.`description`, '; ');
        END IF;
        IF (NEW.`status` <> OLD.`status`) THEN
            SET v_changed = CONCAT(v_changed, 'status = ', NEW.`status`, '; ');
        END IF;
        IF (NEW.`access` <> OLD.`access`) THEN
            SET v_changed = CONCAT(v_changed, 'access = ', NEW.`access`, '; ');
        END IF;
        IF (NEW.`gender` <> OLD.`gender`) THEN
            SET v_changed = CONCAT(v_changed, 'gender = ', NEW.`gender`, '; ');
        END IF;
        IF (NEW.`date_birthday` <> OLD.`date_birthday`) THEN
            SET v_changed = CONCAT(v_changed, 'date_birthday = ', NEW.`date_birthday`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_log`
        (`user_id`,
         `action_user_id`,
         `action`,
         `changed`,
         `date_action`)
        VALUES (OLD.`id`,
                v_user_id,
                2,
                v_changed,
                NOW());
    END IF;
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- trigger user_BEFORE_DELETE
-- -----------------------------------------------------
CREATE
    TRIGGER `user_BEFORE_DELETE`
    BEFORE DELETE ON `user`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_archive INT DEFAULT 3;
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_BEFORE_DELETE';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (@SESSION.user_id IS NOT NULL) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    IF (@SESSION.archive IS NOT NULL) THEN
        SET v_archive = @SESSION.archive;
    END IF;
    INSERT INTO `user_log`
    (`user_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (OLD.`id`,
            v_user_id,
            v_archive,
            '',
            NOW());

    DELETE FROM `user_role`
    WHERE `user_id` = OLD.`id`;
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- table user_log
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_log` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_id` int NOT NULL,
    `action_user_id` int NOT NULL COMMENT '0 - process generation fixtures, > 0 - user_id (administrators)',
    `action` int NOT NULL COMMENT '1 - Insert, 2 - Update, 3 - Delete, 4 - Archive, 5 - Dis-archive',
    `changed` varchar(1024) NULL COMMENT 'list of fields changed with new value',
    `date_action` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- table user_archives
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_archives` (
    `id` int NOT NULL AUTO_INCREMENT,
    `email` varchar(128) NOT NULL,
    `full_name` varchar(256) NOT NULL,
    `description` varchar(1024) NULL,
    `status` int NOT NULL,
    `access` int NOT NULL,
    `gender` int NOT NULL,
    `date_birthday` TIMESTAMP NOT NULL,
    `created_at` TIMESTAMP NOT NULL,
    `updated_at` TIMESTAMP NOT NULL,
    `roles` JSON NOT NULL,
    `slug` varchar(1024) NULL,
    `date_archived` TIMESTAMP NOT NULL,
    `archived_user_id` int NOT NULL  COMMENT '0 - process automatic, > 0 - user_id (administrators)',
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_unicode_ci;

ALTER TABLE `user`
    ADD UNIQUE INDEX `email_idx_archives` (`email`);



-- -----------------------------------------------------
-- trigger user_role_AFTER_INSERT
-- -----------------------------------------------------
CREATE
    TRIGGER `user_role_AFTER_INSERT`
    AFTER INSERT ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_role_AFTER_INSERT';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_role_log`
    (`user_role_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (NEW.`id`,
            v_user_id,
            1,
            '',
            NOW());
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- trigger user_role_AFTER_UPDATE
-- -----------------------------------------------------
CREATE
    TRIGGER `user_role_AFTER_UPDATE`
    AFTER UPDATE ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_changed LONGTEXT DEFAULT '';
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_role_AFTER_UPDATE';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (NEW.`user_id` <> OLD.`user_id` OR
        NEW.`admin_archived_id` <> OLD.`admin_archived_id` OR
        NEW.`role_permission_id` <> OLD.`role_permission_id`)
    THEN
        IF (NEW.`user_id` <> OLD.`user_id`) THEN
            SET v_changed = CONCAT(v_changed, 'user_id = ', NEW.`user_id`, '; ');
        END IF;
        IF (NEW.`admin_archived_id` <> OLD.`admin_archived_id`) THEN
            SET v_changed = CONCAT(v_changed, 'admin_archived_id = ', NEW.`admin_archived_id`, '; ');
        END IF;
        IF (NEW.`role_permission_id` <> OLD.`role_permission_id`) THEN
            SET v_changed = CONCAT(v_changed, 'role_permission_id = ', NEW.`role_permission_id`, '; ');
        END IF;
        IF (@SESSION.user_id IS NOT NULL ) THEN
            SET v_user_id = @SESSION.user_id;
        END IF;
        INSERT INTO `user_role_log`
        (`user_role_id`,
         `action_user_id`,
         `action`,
         `changed`,
         `date_action`)
        VALUES (OLD.`id`,
                v_user_id,
                2,
                v_changed,
                NOW());
    END IF;
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- trigger user_role_BEFORE_DELETE
-- -----------------------------------------------------
CREATE
    TRIGGER `user_role_BEFORE_DELETE`
    BEFORE DELETE ON `user_role`
    FOR EACH ROW
BEGIN
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_name VARCHAR(100) DEFAULT 'trigger user_role_BEFORE_DELETE';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;
    INSERT INTO `user_role_log`
    (`user_role_id`,
     `action_user_id`,
     `action`,
     `changed`,
     `date_action`)
    VALUES (OLD.`id`,
            v_user_id,
            3,
            '',
            NOW());
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- table user_role_log
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `user_role_log` (
    `id` int NOT NULL AUTO_INCREMENT,
    `user_role_id` int NOT NULL,
    `action_user_id` int NOT NULL COMMENT '0 - process generation fixtures, > 0 - user_id (administrators)',
    `action` int NOT NULL COMMENT '1 - Insert, 2 - Update, 3 - Delete',
    `changed` varchar(1024) NULL COMMENT 'list of fields changed with new value',
    `date_action` datetime NOT NULL,
    PRIMARY KEY (`id`)
)
    ENGINE = INNODB,
    AUTO_INCREMENT = 2,
    AVG_ROW_LENGTH = 16384,
    CHARACTER SET utf8mb4,
    COLLATE utf8mb4_unicode_ci;



-- -----------------------------------------------------
-- procedure setUsersNotAccesses
-- -----------------------------------------------------
/*
CALL `setUsersNotAccesses`();
*/
DROP PROCEDURE IF EXISTS `setUsersNotAccesses`;
CREATE PROCEDURE `setUsersNotAccesses`()
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'procedure setUsersNotAccesses';
    DECLARE v_rows_updated INT DEFAULT 0;
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    UPDATE `user`
        SET `access` = 2,
            `updated_at` = NOW()
    WHERE `access` = 1;
    SELECT ROW_COUNT() AS rows_updated INTO v_rows_updated;
    CALL `logWrite`( CONCAT(v_name, ' rows updated = ', v_rows_updated));
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- procedure setUsersAccesses
-- -----------------------------------------------------
/*
CALL `setUsersAccesses`(7);
*/
DROP PROCEDURE IF EXISTS `setUsersAccesses`;
CREATE PROCEDURE `setUsersAccesses`(
    IN vMax INT
)
BEGIN
    DECLARE v_max INT DEFAULT 1;
    DECLARE v_limit INT DEFAULT 0;
    DECLARE v_max_id INT DEFAULT 0;
    DECLARE v_rows_updated INT DEFAULT 0;
    DECLARE v_name VARCHAR(100) DEFAULT 'procedure setUsersAccesses';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    SELECT MAX(id) FROM `user` INTO v_max_id;
    SET vMax = IFNULL(vMax, 5);
    SET v_max = vMax;
    SELECT randomInt(v_max) INTO v_limit;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role_permission rp on rp.`id` = ur.`role_permission_id`
        INNER JOIN role r on r.`id` = rp.`role_id`
    SET u.`access` = 1,
        u.`status` = 1,
        u.`updated_at` = NOW()
    WHERE u.`access` = 2 AND (r.`name` = 'Resident' OR r.`name` = 'Not resident')
      AND u.`id` IN (SELECT `id` FROM (SELECT u2.`id`
                                       FROM `user` u2
                                                INNER JOIN user_role ur2 on u2.`id` = ur2.`user_id`
                                                INNER JOIN role_permission rp2 on rp2.`id` = ur2.`role_permission_id`
                                                INNER JOIN role r2 on r2.`id` = rp2.`role_id`
                                       WHERE u2.`access` = 2 AND (r2.`name` = 'Resident' OR r2.`name` = 'Not resident')
                                         AND u2.`id` >= randomInt(v_max_id)
                                       ORDER BY u2.`id`
                                       LIMIT v_limit) AS subquery);
    SELECT ROW_COUNT() AS rows_updated INTO v_rows_updated;
    CALL `logWrite`( CONCAT(v_name, ' limit = ', v_limit));
    CALL `logWrite`( CONCAT(v_name, ' rows updated = ', v_rows_updated));
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- event setAccesses
-- -----------------------------------------------------
DROP EVENT IF EXISTS `setAccesses`;
CREATE EVENT `setAccesses` ON SCHEDULE
EVERY '1' MINUTE
    STARTS '2023-01-24 00:42:59'
ENABLE
DO
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'event setAccesses';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    CALL setUsersAccesses(5);
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- event setNotAccesses
-- -----------------------------------------------------
DROP EVENT IF EXISTS `setNotAccesses`;
CREATE EVENT `setNotAccesses` ON SCHEDULE
EVERY '1' HOUR
    STARTS '2023-01-24 00:42:59'
ENABLE
DO
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'event setNotAccesses';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    CALL setUsersNotAccesses();
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- procedure setUsersArchives
-- -----------------------------------------------------
/*
CALL `setUsersArchives`();
*/
DROP PROCEDURE IF EXISTS `setUsersArchives`;
CREATE PROCEDURE `setUsersArchives`()
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'procedure setUsersArchives';
    DECLARE v_rows_updated INT DEFAULT 0;
    DECLARE v_max_id INT DEFAULT 0;
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    SELECT MAX(id) FROM `user` INTO v_max_id;
    UPDATE `user` u
        INNER JOIN user_role ur on u.`id` = ur.`user_id`
        INNER JOIN role_permission rp on rp.`id` = ur.`role_permission_id`
        INNER JOIN role r on r.`id` = rp.`role_id`
    SET u.`status` = 2,
        u.`updated_at` = NOW()
    WHERE u.`status` = 1 AND (r.`name` = 'Resident' OR r.`name` = 'Not resident')
      AND u.`id` = randomInt(v_max_id);
    SELECT ROW_COUNT() AS rows_updated INTO v_rows_updated;
    CALL `logWrite`( CONCAT(v_name, ' rows updated = ', v_rows_updated));
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- event setArchives
-- -----------------------------------------------------
DROP EVENT IF EXISTS `setArchives`;
CREATE EVENT `setArchives` ON SCHEDULE
EVERY '3' MINUTE
    STARTS '2023-01-24 00:42:59'
ENABLE
DO
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'event setArchives';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    CALL setUsersArchives();
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- procedure moveUsersArchives
-- -----------------------------------------------------
/*
CALL `moveUsersArchives`();
*/
DROP PROCEDURE IF EXISTS `moveUsersArchives`;
CREATE PROCEDURE `moveUsersArchives`()
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'procedure moveUsersArchives';
    DECLARE v_rows_updated INT DEFAULT 0;
    DECLARE v_admin_id_archived INT DEFAULT 0;
    DECLARE v_user_id INT DEFAULT 0;
    DECLARE v_done integer DEFAULT 0;
    DECLARE v_id decimal(20, 0) DEFAULT 0;
    DECLARE v_email varchar(128) DEFAULT '';
    DECLARE v_full_name varchar(256) DEFAULT '';
    DECLARE v_description varchar(1024) DEFAULT '';
    DECLARE v_status integer DEFAULT 0;
    DECLARE v_access integer DEFAULT 0;
    DECLARE v_gender integer DEFAULT 0;
    DECLARE v_date_birthday DATETIME;
    DECLARE v_created_at DATETIME;
    DECLARE v_updated_at DATETIME;

    DECLARE v_users_cursor CURSOR FOR
        SELECT
            u.`id`,
            u.`email`,
            u.`full_name`,
            u.`description`,
            u.`status`,
            u.`access`,
            u.`gender`,
            u.`date_birthday`,
            u.`created_at`,
            u.`updated_at`
        FROM `user` u
            INNER JOIN user_role ur on u.`id` = ur.`user_id`
            INNER JOIN role_permission rp on rp.`id` = ur.`role_permission_id`
            INNER JOIN role r on r.`id` = rp.`role_id`
        WHERE u.`status` = 2 AND (r.`name` = 'Resident' OR r.`name` = 'Not resident');

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = 1;

    CALL `logWrite`(CONCAT(v_name, ' begin'));
    IF (@SESSION.user_id IS NOT NULL ) THEN
        SET v_user_id = @SESSION.user_id;
    END IF;

    OPEN v_users_cursor;

    users_loop:
    LOOP
        FETCH v_users_cursor INTO v_id, v_email, v_full_name, v_description, 
            v_status, v_access, v_gender, v_date_birthday, v_created_at, v_updated_at;

        IF v_done = 1 THEN
            LEAVE users_loop;
        END IF;

        INSERT INTO `user_archives`
        (`email`,
         `full_name`,
         `description`,
         `status`,
         `access`,
         `gender`,
         `date_birthday`,
         `created_at`,
         `updated_at`,
         `date_archived`,
         `archived_user_id`)
        VALUES (v_email,
                v_full_name,
                v_description,
                v_status,
                v_access,
                v_gender,
                v_date_birthday,
                v_created_at,
                v_updated_at,
                NOW(),
                v_user_id);
        SET v_admin_id_archived = LAST_INSERT_ID();

        UPDATE `user_role`
            SET `user_id` = 0,
                `admin_archived_id` = v_admin_id_archived
        WHERE `user_id` = v_id;

        SET @SESSION.archive = 4;
        DELETE FROM `user` WHERE `id` = v_id;
        SET v_rows_updated = v_rows_updated + 1;
    END LOOP users_loop;
    CLOSE v_users_cursor;
    CALL `logWrite`( CONCAT(v_name, ' rows move = ', v_rows_updated));
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- event moveArchives
-- -----------------------------------------------------
DROP EVENT IF EXISTS `moveArchives`;
CREATE EVENT `moveArchives` ON SCHEDULE
EVERY '1' HOUR
    STARTS '2023-01-24 00:42:59'
ENABLE
DO
BEGIN
    DECLARE v_name VARCHAR(100) DEFAULT 'event moveArchives';
    CALL `logWrite`(CONCAT(v_name, ' begin'));
    CALL moveUsersArchives();
    CALL `logWrite`(CONCAT(v_name, ' end'));
END;


-- -----------------------------------------------------
-- function randomInt
-- -----------------------------------------------------
/*
CALL `randomInt`(100);
*/
DROP FUNCTION IF EXISTS `randomInt`;
CREATE FUNCTION `randomInt`(count INT) RETURNS INT
BEGIN
    DECLARE v_result INT DEFAULT 0;
    DECLARE v_name VARCHAR(100) DEFAULT 'procedure randomInt';
#     CALL `logWrite`(CONCAT(v_name, ' begin'));
    SELECT FLOOR((RAND() * count)) INTO v_result;
    #     CALL `logWrite`(CONCAT(v_name, ' count = ', count));
#     CALL `logWrite`(CONCAT(v_name, ' result = ', v_result));
#     CALL `logWrite`(CONCAT(v_name, ' end'));

    RETURN v_result;
END;