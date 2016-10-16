DROP TABLE IF EXISTS `oc_litemf_orders`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_orders` (
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
        `status` ENUM('send', 'unsend') NOT NULL,
        `user_id` INT NOT NULL,
        `order_id` INT NOT NULL,
        `delivery_method_id` INT NOT NULL,
        `delivery_point_id` INT DEFAULT NULL,
        `tracking` CHAR(30) CHARACTER SET utf8 DEFAULT NULL,
        `address_id` INT DEFAULT NULL,
        `outgoing_package_id` INT DEFAULT NULL,
        `incoming_packages` CHAR(255) CHARACTER SET utf8 DEFAULT NULL,
        `litemf_address_id` INT DEFAULT NULL,
        PRIMARY KEY(`id`)
    );
DROP TABLE IF EXISTS `oc_litemf_address`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_address` (
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
        `user_id` INT DEFAULT NULL,
        `first_name` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `last_name` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `middle_name` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `street` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `house` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `city` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `region` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `zip_code` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `phone` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `series` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `number` CHAR(30) CHARACTER SET utf8 NOT NULL,
        `issue_date` datetime NOT NULL,
        `issued_by` CHAR(255) CHARACTER SET utf8 NOT NULL,
        PRIMARY KEY(`id`)
    );
DROP TABLE IF EXISTS `oc_litemf_courier_address`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_courier_address` (
    `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `litemf_orders` INT NOT NULL,
    `street` CHAR(30) CHARACTER SET utf8 NOT NULL,
    `email` CHAR(30) CHARACTER SET utf8 NOT NULL,
    `house` CHAR(30) CHARACTER SET utf8 NOT NULL,
    `number` CHAR(30) CHARACTER SET utf8 NOT NULL,
    `phone` CHAR(30) CHARACTER SET utf8 NOT NULL,
    PRIMARY KEY(`id`)
);