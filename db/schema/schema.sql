
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
	`idFormularioAcceso` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idPerfilAcceso` INTEGER,
	`idFormularioApp` INTEGER,
	PRIMARY KEY(`idFormularioAcceso`)
);


CREATE TABLE IF NOT EXISTS `colaborador` (
	`idColaborador` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idEmpresa` INTEGER,
	`idLocalEmpresa` INTEGER NOT NULL,
	PRIMARY KEY(`idColaborador`)
);


CREATE TABLE IF NOT EXISTS `estadoAviso` (
	`idEstadoAviso` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`textoEstado` VARCHAR(20),
	PRIMARY KEY(`idEstadoAviso`)
);


CREATE TABLE IF NOT EXISTS `estadoAvisoFormulario` (
	`idAvisoFormulario` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idEstadoAviso` INTEGER,
	`idFormularioAcceso` INTEGER,
	PRIMARY KEY(`idAvisoFormulario`)
);


CREATE TABLE IF NOT EXISTS `pedidoLocal` (
	`idPedidoLocal` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idLocalEmpresa` INTEGER,
	`idEmpresa` INTEGER,
	`pedidoLocal` VARCHAR(50),
	`QRLocal` TEXT(1024),
	PRIMARY KEY(`idPedidoLocal`)
);


CREATE TABLE IF NOT EXISTS `metricaPedido` (
	`idMetricaPedido` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
	`idPedidoLocal` INTEGER,
	`idFormularioApp` INTEGER,
	`idEstadoAviso` INTEGER,
	`fechaMetrica` DATE,
	`HoraInicio` TIMESTAMP,
	`HoraFin` TIMESTAMP,
	PRIMARY KEY(`idMetricaPedido`)
);


ALTER TABLE `empresaContrata` ADD FOREIGN KEY(`idEmpresa`) REFERENCES `localEmpresa`(`idEmpresa`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `localEmpresa` ADD FOREIGN KEY(`idLocalEmpresa`) REFERENCES `usersAceso`(`idLocalEmpresa`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `users` ADD FOREIGN KEY(`id`) REFERENCES `usersAceso`(`idUser`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfilAcceso` ADD FOREIGN KEY(`idPerfilAcceso`) REFERENCES `usersAceso`(`idPerfilAcceso`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `formularioApp` ADD FOREIGN KEY(`idFormularioApp`) REFERENCES `formularioAcceso`(`idFormularioApp`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `perfilAcceso` ADD FOREIGN KEY(`idPerfilAcceso`) REFERENCES `formularioAcceso`(`idPerfilAcceso`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `localEmpresa` ADD FOREIGN KEY(`idLocalEmpresa`, `idEmpresa`) REFERENCES `colaborador`(`idLocalEmpresa`, `idEmpresa`) ON UPDATE NO ACTION ON DELETE NO ACTION;
-- ALTER TABLE `colaborador` ADD FOREIGN KEY(`idColaborador`) REFERENCES `users`(`idColaborador`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `estadoAviso` ADD FOREIGN KEY(`idEstadoAviso`) REFERENCES `estadoAvisoFormulario`(`idEstadoAviso`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `formularioApp` ADD FOREIGN KEY(`idFormularioApp`) REFERENCES `estadoAvisoFormulario`(`idFormularioAcceso`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `localEmpresa` ADD FOREIGN KEY(`idLocalEmpresa`, `idEmpresa`) REFERENCES `pedidoLocal`(`idLocalEmpresa`, `idEmpresa`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `pedidoLocal` ADD FOREIGN KEY(`idPedidoLocal`) REFERENCES `metricaPedido`(`idPedidoLocal`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `estadoAviso` ADD FOREIGN KEY(`idEstadoAviso`) REFERENCES `metricaPedido`(`idEstadoAviso`) ON UPDATE NO ACTION ON DELETE NO ACTION;
ALTER TABLE `formularioApp` ADD FOREIGN KEY(`idFormularioApp`) REFERENCES `metricaPedido`(`idEstadoAviso`) ON UPDATE NO ACTION ON DELETE NO ACTION;