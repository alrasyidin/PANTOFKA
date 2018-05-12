-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema final_project_pantofka
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema final_project_pantofka
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `final_project_pantofka` DEFAULT CHARACTER SET utf8 ;
USE `final_project_pantofka` ;

-- -----------------------------------------------------
-- Table `final_project_pantofka`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`categories` (
  `category_id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `parent_id` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  UNIQUE INDEX `subcategory_UNIQUE` (`name` ASC),
  INDEX `cat_par_fk_idx` (`parent_id` ASC),
  CONSTRAINT `cat_par_fk`
    FOREIGN KEY (`parent_id`)
    REFERENCES `final_project_pantofka`.`categories` (`category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 13
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`colors`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`colors` (
  `color_id` INT(11) NOT NULL AUTO_INCREMENT,
  `color` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`color_id`),
  UNIQUE INDEX `color_UNIQUE` (`color` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`genders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`genders` (
  `gender_id` INT(11) NOT NULL AUTO_INCREMENT,
  `gender` VARCHAR(1) NOT NULL,
  PRIMARY KEY (`gender_id`))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`materials`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`materials` (
  `material_id` INT(11) NOT NULL AUTO_INCREMENT,
  `material` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`material_id`),
  UNIQUE INDEX `material_UNIQUE` (`material` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`carts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`carts` (
  `cart_id` INT NOT NULL AUTO_INCREMENT,
  `user_id` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`cart_id`),
  UNIQUE INDEX `user_id_UNIQUE` (`user_id` ASC),
  UNIQUE INDEX `cart_id_UNIQUE` (`cart_id` ASC))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`users` (
  `user_id` INT(11) NOT NULL AUTO_INCREMENT,
  `email` VARCHAR(45) NOT NULL,
  `first_name` VARCHAR(45) NOT NULL,
  `last_name` VARCHAR(45) NOT NULL,
  `password` VARCHAR(45) NOT NULL,
  `is_admin` TINYINT(4) NULL DEFAULT '0',
  `gender_id` INT(11) NOT NULL,
  `carts_cart_id` INT NOT NULL,
  PRIMARY KEY (`user_id`, `carts_cart_id`),
  INDEX `fk_users_genders1_idx` (`gender_id` ASC),
  INDEX `fk_users_carts1_idx` (`carts_cart_id` ASC),
  CONSTRAINT `fk_users_genders1`
    FOREIGN KEY (`gender_id`)
    REFERENCES `final_project_pantofka`.`genders` (`gender_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_carts1`
    FOREIGN KEY (`carts_cart_id`)
    REFERENCES `final_project_pantofka`.`carts` (`cart_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 36
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`orders`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`orders` (
  `order_id` INT(11) NOT NULL AUTO_INCREMENT,
  `order` INT(10) UNSIGNED NULL DEFAULT NULL,
  `total_price` DOUBLE NOT NULL,
  `user_id` INT(11) NOT NULL,
  `date` DATE NOT NULL,
  PRIMARY KEY (`order_id`),
  INDEX `fk_orders_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_orders_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `final_project_pantofka`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 140
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`products` (
  `product_id` INT(11) NOT NULL AUTO_INCREMENT,
  `product_name` VARCHAR(45) NOT NULL,
  `price` DOUBLE UNSIGNED ZEROFILL NOT NULL,
  `info` VARCHAR(500) NULL DEFAULT 'null',
  `product_image_url` VARCHAR(100) NOT NULL,
  `promo_percentage` DOUBLE NULL DEFAULT '0',
  `color_id` INT(11) NOT NULL,
  `category_id` INT(11) NOT NULL,
  `material_id` INT(11) NOT NULL,
  PRIMARY KEY (`product_id`, `product_name`, `price`, `material_id`, `color_id`, `category_id`),
  UNIQUE INDEX `product_id_UNIQUE` (`product_id` ASC),
  INDEX `fk_products_colors_idx` (`color_id` ASC),
  INDEX `fk_products_subcategory1_idx` (`category_id` ASC),
  INDEX `fk_products_materials1_idx` (`material_id` ASC),
  CONSTRAINT `fk_products_colors`
    FOREIGN KEY (`color_id`)
    REFERENCES `final_project_pantofka`.`colors` (`color_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_materials1`
    FOREIGN KEY (`material_id`)
    REFERENCES `final_project_pantofka`.`materials` (`material_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_subcategory1`
    FOREIGN KEY (`category_id`)
    REFERENCES `final_project_pantofka`.`categories` (`category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
AUTO_INCREMENT = 20
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`sizes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`sizes` (
  `size_id` INT(11) NOT NULL AUTO_INCREMENT,
  `size_number` INT(11) NOT NULL,
  PRIMARY KEY (`size_id`),
  UNIQUE INDEX `size_number_UNIQUE` (`size_number` ASC))
ENGINE = InnoDB
AUTO_INCREMENT = 24
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`orders_has_products`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`orders_has_products` (
  `product_id` INT(11) NOT NULL,
  `order_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  `size_id` INT(11) NOT NULL,
  PRIMARY KEY (`product_id`, `order_id`, `size_id`),
  INDEX `fk_products_has_orders_orders1_idx` (`order_id` ASC),
  INDEX `fk_products_has_orders_products1_idx` (`product_id` ASC),
  INDEX `fk_size_fk_idx` (`size_id` ASC),
  CONSTRAINT `fk_products_has_orders_orders1`
    FOREIGN KEY (`order_id`)
    REFERENCES `final_project_pantofka`.`orders` (`order_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_orders_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `final_project_pantofka`.`products` (`product_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_size_fk`
    FOREIGN KEY (`size_id`)
    REFERENCES `final_project_pantofka`.`sizes` (`size_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`products_has_sizes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`products_has_sizes` (
  `product_id` INT(11) NOT NULL,
  `size_id` INT(11) NOT NULL,
  `quantity` INT(11) NOT NULL,
  PRIMARY KEY (`product_id`, `size_id`),
  INDEX `fk_products_has_sizes_sizes1_idx` (`size_id` ASC),
  INDEX `fk_products_has_sizes_products1_idx` (`product_id` ASC),
  CONSTRAINT `fk_products_has_sizes_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `final_project_pantofka`.`products` (`product_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_sizes_sizes1`
    FOREIGN KEY (`size_id`)
    REFERENCES `final_project_pantofka`.`sizes` (`size_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`ratings`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`ratings` (
  `product_id` INT(11) NOT NULL,
  `user_id` INT(11) NOT NULL,
  `rating_value` INT(11) NOT NULL,
  PRIMARY KEY (`product_id`, `user_id`),
  INDEX `fk_products_has_users_users1_idx` (`user_id` ASC),
  INDEX `fk_products_has_users_products1_idx` (`product_id` ASC),
  CONSTRAINT `fk_products_has_users_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `final_project_pantofka`.`products` (`product_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_users_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `final_project_pantofka`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`users_has_favorites`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`users_has_favorites` (
  `user_id` INT(11) NOT NULL,
  `product_id` INT(11) NOT NULL,
  PRIMARY KEY (`user_id`, `product_id`),
  INDEX `fk_users_has_products_products1_idx` (`product_id` ASC),
  INDEX `fk_users_has_products_users1_idx` (`user_id` ASC),
  CONSTRAINT `fk_users_has_products_products1`
    FOREIGN KEY (`product_id`)
    REFERENCES `final_project_pantofka`.`products` (`product_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_users_has_products_users1`
    FOREIGN KEY (`user_id`)
    REFERENCES `final_project_pantofka`.`users` (`user_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `final_project_pantofka`.`products_has_carts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `final_project_pantofka`.`products_has_carts` (
  `cart_id` INT NOT NULL,
  `product_id` INT(11) NOT NULL,
  `product_name` VARCHAR(45) NOT NULL,
  `product_price` DOUBLE UNSIGNED ZEROFILL NOT NULL,
  `product_material_id` INT(11) NOT NULL,
  `product_color_id` INT(11) NOT NULL,
  `product_category_id` INT(11) NOT NULL,
  PRIMARY KEY (`cart_id`, `product_id`, `product_name`, `product_price`, `product_material_id`, `product_color_id`, `product_category_id`),
  INDEX `fk_products_has_carts_carts1_idx` (`cart_id` ASC),
  INDEX `fk_products_has_carts_products1_idx` (`product_id` ASC, `product_name` ASC, `product_price` ASC, `product_material_id` ASC, `product_color_id` ASC, `product_category_id` ASC),
  CONSTRAINT `fk_products_has_carts_products1`
    FOREIGN KEY (`product_id` , `product_name` , `product_price` , `product_material_id` , `product_color_id` , `product_category_id`)
    REFERENCES `final_project_pantofka`.`products` (`product_id` , `product_name` , `price` , `material_id` , `color_id` , `category_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_products_has_carts_carts1`
    FOREIGN KEY (`cart_id`)
    REFERENCES `final_project_pantofka`.`carts` (`cart_id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
