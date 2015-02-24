
DROP TABLE IF EXISTS `card`; 
DROP TABLE IF EXISTS `users`; 
DROP TABLE IF EXISTS `accesslog`; 
DROP TABLE IF EXISTS `profil`; 

CREATE TABLE IF NOT EXISTS `eserrure`.`card` (
	`idcard` INT NOT NULL AUTO_INCREMENT,
	`idkey` VARCHAR(45) NOT NULL,
	PRIMARY KEY (`idcard`)
)ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `mydb`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eserrure`.`users` (
  `iduser` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(45) NOT NULL,
  `lastname` VARCHAR(45) NOT NULL,
  `idcard` INT NOT NULL REFERENCES card(`idcard`)
   ON DELETE CASCADE ON UPDATE CASCADE,
  `permission` INT NOT NULL,
   PRIMARY KEY (`iduser`),
   UNIQUE KEY `idcard` (`idcard`)
  )ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`accesslog`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `eserrure`.`accesslog` (
  `idaccesslog` INT NOT NULL AUTO_INCREMENT,
  `firstname` VARCHAR(45) NULL,
  `lastname` VARCHAR(45) NULL,
  `idkey` VARCHAR(45) NOT NULL REFERENCES card(`idkey`)
   ON DELETE CASCADE ON UPDATE CASCADE,
  `date` DATETIME NOT NULL,
  `permission` INT NULL,
  PRIMARY KEY (`idaccesslog`),
  UNIQUE KEY `idaccesslog` (`idaccesslog`)
)ENGINE = InnoDB;




CREATE TABLE IF NOT EXISTS `eserrure`.`profil` (
  `idprofil` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `startTime` DATETIME NOT NULL,
  `stopTime`  DATETIME NOT NULL,
  PRIMARY KEY (`idprofil`)
) ENGINE=InnoDB;


INSERT INTO `card` VALUES(DEFAULT,'20122565');
INSERT INTO `card` VALUES(DEFAULT,'20142565');
INSERT INTO `card` VALUES(DEFAULT,'230219255');
INSERT INTO `users` VALUES(DEFAULT,'ABDRASSOUL','YOUSSOUF',1,1);
INSERT INTO `users` VALUES(DEFAULT,'OSINUNGA','BENGA',2,1);


INSERT INTO `accesslog` VALUES(DEFAULT,'OSINUNGA','BENGA',20122565,CURDATE(),1);
INSERT INTO `accesslog` VALUES(DEFAULT,'ABDRASSOUL','YOUSSOUF',20142302,NOW(),1);



