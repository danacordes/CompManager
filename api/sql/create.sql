-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema beercomp
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema beercomp
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `beercomp` DEFAULT CHARACTER SET utf8 ;
USE `beercomp` ;

-- -----------------------------------------------------
-- Table `beercomp`.`user`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`user` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(200) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `club` VARCHAR(255) NULL,
  `name` VARCHAR(255) NULL,
  `address_1` VARCHAR(255) NULL,
  `address_2` VARCHAR(255) NULL,
  `city` VARCHAR(255) NULL,
  `state` VARCHAR(45) NULL,
  `zip` VARCHAR(45) NULL,
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC),
  PRIMARY KEY (`id`),
  UNIQUE INDEX `email_UNIQUE` (`email` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`entry`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`entry` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `style_id` INT NOT NULL,
  `competition_id` INT NOT NULL,
  `name` VARCHAR(255) NULL,
  `judging_number` VARCHAR(45) NULL,
  `score` INT NULL,
  `received` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `payment_confirmation` VARCHAR(255) NULL,
  `payment_timestamp` TIMESTAMP NULL,
  `payment_type` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`competition`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`competition` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `awards_start` TIMESTAMP NULL,
  `volunteer_start` TIMESTAMP NULL,
  `volunteer_end` TIMESTAMP NULL,
  `registration_start` TIMESTAMP NULL,
  `registration_end` TIMESTAMP NULL,
  `rules` TEXT NULL,
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  `organization_id` INT NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`style`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`style` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `style_type_id` INT NOT NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  `parent_style_id` INT NULL DEFAULT 0,
  `identifier` VARCHAR(45) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`location`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`location` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `address_1` VARCHAR(255) NULL,
  `address_2` VARCHAR(255) NULL,
  `city` VARCHAR(255) NULL,
  `state` VARCHAR(45) NULL,
  `user_id` VARCHAR(45) NULL COMMENT '\'hosts id\'',
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`round`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`round` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `sequence` TINYINT NOT NULL,
  `start` TIMESTAMP NULL,
  `location_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`table`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`table` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NULL,
  `round_id` INT NOT NULL,
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`tables_styles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`tables_styles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `table_id` INT NOT NULL,
  `style_id` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`award`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`award` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `table_id` INT NOT NULL,
  `name` VARCHAR(45) NOT NULL,
  `place` VARCHAR(45) NULL,
  `award` TINYINT NULL,
  `BOS` TINYINT NULL DEFAULT 0,
  `updated_at` TIMESTAMP NULL,
  `created_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`volunteer`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`volunteer` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `competition_id` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`organization`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`organization` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`organizations_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`organizations_roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `organization_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `role_type` INT NOT NULL COMMENT 'admin, entrant, judge, steward, etc\n',
  `created_at` TIMESTAMP NULL,
  `update_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`, `organization_id`, `user_id`, `role_type`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`styles_competitions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`styles_competitions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `style_id` INT NOT NULL,
  `competition_id` INT NOT NULL,
  PRIMARY KEY (`id`, `style_id`, `competition_id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`competitions_roles`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`competitions_roles` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `competition_id` INT NOT NULL,
  `role_type` INT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`, `user_id`, `competition_id`, `role_type`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`entry_attributes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`entry_attributes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `style_type_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `field_name` VARCHAR(45) NOT NULL,
  `is_required` TINYINT NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`entry_values`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`entry_values` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `attribute_id` INT NOT NULL,
  `value` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `beercomp`.`style_type`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `beercomp`.`style_type` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
