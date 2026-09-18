CREATE TABLE IF NOT EXISTS `cache` (
	`key` VARCHAR(255) NOT NULL,
	`value` MEDIUMTEXT NOT NULL,
	`expiration` INTEGER NOT NULL,
	PRIMARY KEY(`key`)
);


CREATE TABLE IF NOT EXISTS `cache_locks` (
	`key` VARCHAR(255) NOT NULL,
	`owner` VARCHAR(255) NOT NULL,
	`expiration` INTEGER NOT NULL,
	PRIMARY KEY(`key`)
);


CREATE TABLE IF NOT EXISTS `failed_jobs` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`uuid` VARCHAR(255) NOT NULL,
	`connection` TEXT NOT NULL,
	`queue` TEXT NOT NULL,
	`payload` LONGTEXT NOT NULL,
	`exception` LONGTEXT NOT NULL,
	`failed_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
	PRIMARY KEY(`id`),
	CONSTRAINT `failed_jobs_uuid_unique` UNIQUE (`uuid`)
);


CREATE TABLE IF NOT EXISTS `job_batches` (
	`id` VARCHAR(255) NOT NULL,
	`name` VARCHAR(255) NOT NULL,
	`total_jobs` INTEGER NOT NULL,
	`pending_jobs` INTEGER NOT NULL,
	`failed_jobs` INTEGER NOT NULL,
	`failed_job_ids` LONGTEXT NOT NULL,
	`options` MEDIUMTEXT,
	`cancelled_at` INTEGER DEFAULT NULL,
	`created_at` INTEGER NOT NULL,
	`finished_at` INTEGER DEFAULT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `jobs` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`queue` VARCHAR(255) NOT NULL,
	`payload` LONGTEXT NOT NULL,
	`attempts` TINYINT NOT NULL,
	`reserved_at` INTEGER DEFAULT NULL,
	`available_at` INTEGER NOT NULL,
	`created_at` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `migrations` (
	`id` INTEGER NOT NULL AUTO_INCREMENT,
	`migration` VARCHAR(255) NOT NULL,
	`batch` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
	`email` VARCHAR(255) NOT NULL,
	`token` VARCHAR(255) NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT NULL,
	PRIMARY KEY(`email`)
);


CREATE TABLE IF NOT EXISTS `sessions` (
	`id` VARCHAR(255) NOT NULL,
	`user_id` BIGINT DEFAULT NULL,
	`ip_address` VARCHAR(45) DEFAULT NULL,
	`user_agent` TEXT,
	`payload` LONGTEXT NOT NULL,
	`last_activity` INTEGER NOT NULL,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `users` (
	`id` BIGINT NOT NULL AUTO_INCREMENT,
	`name` VARCHAR(255) NOT NULL,
	`email` VARCHAR(255) NOT NULL,
	`email_verified_at` TIMESTAMP NOT NULL DEFAULT NULL,
	`password` VARCHAR(255) NOT NULL,
	`remember_token` VARCHAR(100) DEFAULT NULL,
	`idColaborador` INTEGER NOT NULL,
	`created_at` TIMESTAMP NOT NULL DEFAULT NULL,
	`updated_at` TIMESTAMP NOT NULL DEFAULT NULL,
	PRIMARY KEY(`id`),
	CONSTRAINT `users_email_unique` UNIQUE (`email`)
);


CREATE TABLE IF NOT EXISTS `localEmpresa` (
	`idLocalEmpresa` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idEmpresa` INTEGER,
	PRIMARY KEY(`idLocalEmpresa`)
);


CREATE TABLE IF NOT EXISTS `empresaContrata` (
	`idEmpresa` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,
	PRIMARY KEY(`idEmpresa`)
);


CREATE TABLE IF NOT EXISTS `usersAceso` (
	`idUsersAceso` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idLocalEmpresa` INTEGER,
	`idUser` BIGINT,
	`idPerfilAcceso` INTEGER,
	PRIMARY KEY(`idUsersAceso`)
);


CREATE TABLE IF NOT EXISTS `perfilAcceso` (
	`idPerfilAcceso` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(`idPerfilAcceso`)
);


CREATE TABLE IF NOT EXISTS `formularioApp` (
	`idFormularioApp` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	PRIMARY KEY(`idFormularioApp`)
);


CREATE TABLE IF NOT EXISTS `formularioAcceso` (
	`id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idFormularioApp` INTEGER,
	`idPerfilAcceso` INTEGER,
	PRIMARY KEY(`id`)
);


CREATE TABLE IF NOT EXISTS `colaborador` (
	`idColaborador` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idEmpresa` INTEGER NOT NULL,
	`idLocalEmpresa` INTEGER NOT NULL,
	PRIMARY KEY(`idColaborador`)
);


ALTER TABLE `empresaContrata`
ADD FOREIGN KEY(`idEmpresa`) REFERENCES `localEmpresa`(`idEmpresa`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `localEmpresa`
ADD FOREIGN KEY(`idLocalEmpresa`) REFERENCES `usersAceso`(`idLocalEmpresa`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `users`
ADD FOREIGN KEY(`id`) REFERENCES `usersAceso`(`idUser`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfilAcceso`
ADD FOREIGN KEY(`idPerfilAcceso`) REFERENCES `usersAceso`(`idPerfilAcceso`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `formularioApp`
ADD FOREIGN KEY(`idFormularioApp`) REFERENCES `formularioAcceso`(`idFormularioApp`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfilAcceso`
ADD FOREIGN KEY(`idPerfilAcceso`) REFERENCES `formularioAcceso`(`idPerfilAcceso`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `empresaContrata`
ADD FOREIGN KEY(`idEmpresa`) REFERENCES `colaborador`(`idEmpresa`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `localEmpresa`
ADD FOREIGN KEY(`idLocalEmpresa`) REFERENCES `colaborador`(`idLocalEmpresa`)
ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `colaborador`
ADD FOREIGN KEY(`idColaborador`) REFERENCES `users`(`idColaborador`)
ON UPDATE NO ACTION ON DELETE NO ACTION;