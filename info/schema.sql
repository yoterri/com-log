-- MySQL Script generated by MySQL Workbench
-- lun 08 abr 2019 11:18:42 -04
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema builder
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Table `log`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `log` (
  `id` BIGINT NOT NULL AUTO_INCREMENT,
  `type` CHAR(50) NOT NULL,
  `created_on` DATETIME NOT NULL,
  `timezone` VARCHAR(50) NOT NULL,
  `method` VARCHAR(150) NULL,
  `ref` CHAR(250) NULL,
  `message` CHAR(250) NULL,
  `extra` LONGTEXT NULL,
  `get` TEXT NULL,
  `post` TEXT NULL,
  `headers` TEXT NULL,
  `body` LONGTEXT NULL,
  `url` TEXT NULL,
  `file` LONGTEXT NULL,
  `cookies` TEXT NULL,
  `session` TEXT NULL,
  `agent` TEXT NULL,
  `ip_address` VARCHAR(100) NULL,
  PRIMARY KEY (`id`),
  INDEX `type` (`type` ASC),
  INDEX `created_on` (`created_on` ASC),
  INDEX `ref` (`ref` ASC))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;