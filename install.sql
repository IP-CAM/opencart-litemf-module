DROP TABLE IF EXISTS `oc_litemf_orders`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_orders` (
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
        `status` ENUM('send', 'unsend') NOT NULL,
        `user_id` INT NOT NULL,
        `order_id` INT NOT NULL,
        `delivery_method_id` INT NOT NULL,
        `delivery_point_id` INT DEFAULT NULL,
        `tracking` CHAR(30) DEFAULT NULL,
        `address_id` INT DEFAULT NULL,
        `outgoing_package_id` INT DEFAULT NULL,
        `incoming_packages` CHAR(255) DEFAULT NULL,
        `litemf_address_id` INT DEFAULT NULL,
        PRIMARY KEY(`id`)
    );
DROP TABLE IF EXISTS `oc_litemf_address`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_address` (
        `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
        `user_id` INT DEFAULT NULL,
        `first_name` CHAR(30) DEFAULT NULL,
        `last_name` CHAR(30) DEFAULT NULL,
        `middle_name` CHAR(30) DEFAULT NULL,
        `street` CHAR(30) DEFAULT NULL,
        `house` CHAR(30) DEFAULT NULL,
        `country` CHAR(30) DEFAULT NULL,
        `city` CHAR(30) DEFAULT NULL,
        `region` CHAR(30) DEFAULT NULL,
        `zip_code` CHAR(30) DEFAULT NULL,
        `phone` CHAR(30) DEFAULT NULL,
        `series` CHAR(30) DEFAULT NULL,
        `number` CHAR(30) DEFAULT NULL,
        `issue_date` datetime DEFAULT NULL,
        `issued_by` CHAR(255) DEFAULT NULL,
        `address_line_1` CHAR(255) DEFAULT NULL,
        `address_line_2` CHAR(255) DEFAULT NULL,
        PRIMARY KEY(`id`)
    );
DROP TABLE IF EXISTS `oc_litemf_courier_address`;
CREATE TABLE IF NOT EXISTS
    `oc_litemf_courier_address` (
    `id` MEDIUMINT NOT NULL AUTO_INCREMENT,
    `litemf_orders` INT NOT NULL,
    `street` CHAR(30) NOT NULL,
    `email` CHAR(30) NOT NULL,
    `house` CHAR(30) NOT NULL,
    `number` CHAR(30) NOT NULL,
    `phone` CHAR(30) NOT NULL,
    PRIMARY KEY(`id`)
);