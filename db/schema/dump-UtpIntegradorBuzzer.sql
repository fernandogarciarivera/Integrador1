
DROP TABLE IF EXISTS `clientes`;
CREATE TABLE `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `preferencias_notificacion` json DEFAULT NULL,
  `fecha_ultima_visita` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_clientes_telefono` (`telefono`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `perfilAccesos`;
CREATE TABLE `perfilAccesos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `perfil` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `formularios`;
CREATE TABLE `formularios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `formulario` varchar(45) NOT NULL,
  `controller` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `formulario_UNIQUE` (`formulario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `restaurantes`;
CREATE TABLE `restaurantes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `plan` enum('BASICO','PRO','ENTERPRISE') NOT NULL DEFAULT 'BASICO',
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_restaurantes_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `locales`;
CREATE TABLE `locales` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `direccion` varchar(200) DEFAULT NULL,
  `codigo` varchar(20) DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') NOT NULL DEFAULT 'ACTIVO',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_locales_restaurante_codigo` (`restaurante_id`,`codigo`),
  KEY `idx_locales_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_locales_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `trabajadores`;
CREATE TABLE `trabajadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `restaurante_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned DEFAULT NULL,
  `rol` enum('SUPER_ADMIN','ADMIN','GERENTE','CAJA','COCINA') NOT NULL,
  `puesto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_trabajador_user_local` (`user_id`,`restaurante_id`,`local_id`),
  KEY `idx_trabajadores_local` (`local_id`),
  KEY `idx_trabajadores_restaurante` (`restaurante_id`),
  KEY `idx_trabajadores_rol` (`rol`),
  CONSTRAINT `fk_trabajadores_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_trabajadores_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_trabajadores_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `productos`;
CREATE TABLE `productos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `local_id` bigint unsigned NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text,
  `precio` decimal(10,2) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `disponible` tinyint(1) NOT NULL DEFAULT '1',
  `url_imagen` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_productos_local` (`local_id`),
  KEY `idx_productos_categoria` (`categoria`),
  CONSTRAINT `fk_productos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `pedidos`;
CREATE TABLE `pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `local_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `trabajador_caja_id` bigint unsigned DEFAULT NULL,
  `codigo_pedido` varchar(20) NOT NULL,
  `codigo_qr` varchar(100) DEFAULT NULL,
  `imagen_qr` varchar(200) DEFAULT NULL,
  `fecha_expira_qr` timestamp NULL DEFAULT NULL,
  `tipo` enum('PRESENCIAL','PARA_LLEVAR') NOT NULL DEFAULT 'PRESENCIAL',
  `estado` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') NOT NULL DEFAULT 'REGISTRADO',
  `fecha_pedido` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `tiempo_preparacion_estimado` int DEFAULT NULL,
  `tiempo_preparacion_real` int DEFAULT NULL,
  `total` decimal(10,2) DEFAULT NULL,
  `notas` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_pedidos_codigo` (`codigo_pedido`),
  KEY `idx_pedidos_local_estado` (`local_id`,`estado`),
  KEY `idx_pedidos_fecha` (`fecha_pedido`),
  KEY `idx_pedidos_cliente` (`cliente_id`),
  KEY `fk_pedidos_trabajador_caja` (`trabajador_caja_id`),
  CONSTRAINT `fk_pedidos_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_local` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_pedidos_trabajador_caja` FOREIGN KEY (`trabajador_caja_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `detalle_pedidos`;
CREATE TABLE `detalle_pedidos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `producto_id` bigint unsigned NOT NULL,
  `cantidad` int unsigned NOT NULL DEFAULT '1',
  `precio_unitario` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `instrucciones_especiales` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_detalle_pedido` (`pedido_id`),
  KEY `idx_detalle_producto` (`producto_id`),
  CONSTRAINT `fk_detalle_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_detalle_producto` FOREIGN KEY (`producto_id`) REFERENCES `productos` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `historial_estados`;
CREATE TABLE `historial_estados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `trabajador_id` bigint unsigned DEFAULT NULL,
  `estado_anterior` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') DEFAULT NULL,
  `estado_nuevo` enum('REGISTRADO','PREPARANDO','LISTO','ENTREGADO','CANCELADO') NOT NULL,
  `fecha_cambio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_hist_pedido_fecha` (`pedido_id`,`fecha_cambio`),
  KEY `idx_hist_trabajador` (`trabajador_id`),
  CONSTRAINT `fk_hist_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_hist_trabajador` FOREIGN KEY (`trabajador_id`) REFERENCES `trabajadores` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `metricas`;
CREATE TABLE `metricas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `restaurante_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `total_pedidos` int unsigned NOT NULL DEFAULT '0',
  `tiempo_promedio_espera` decimal(5,2) DEFAULT NULL,
  `porcentaje_notificaciones_exitosas` decimal(5,2) DEFAULT NULL,
  `pedidos_gestionados_qr` int unsigned NOT NULL DEFAULT '0',
  `clientes_unicos` int unsigned NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_metrica_rest_fecha` (`restaurante_id`,`fecha`),
  KEY `fk_metricas_locales1_idx` (`local_id`),
  CONSTRAINT `fk_metrica_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_metricas_locales1` FOREIGN KEY (`local_id`) REFERENCES `locales` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `notificaciones`;
CREATE TABLE `notificaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `pedido_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `tipo` enum('SONIDO','VIBRACION','VISUAL','PUSH') NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `fecha_envio` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_lectura` timestamp NULL DEFAULT NULL,
  `estado` enum('ENVIADA','ENTREGADA','LEIDA','FALLIDA') NOT NULL DEFAULT 'ENVIADA',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_notif_pedido` (`pedido_id`),
  KEY `idx_notif_cliente` (`cliente_id`),
  KEY `idx_notif_estado` (`estado`),
  CONSTRAINT `fk_notif_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_notif_pedido` FOREIGN KEY (`pedido_id`) REFERENCES `pedidos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;


DROP TABLE IF EXISTS `perfilesFormularios`;
CREATE TABLE `perfilesFormularios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `perfilAcceso_id` bigint unsigned NOT NULL,
  `formulario_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`id`,`perfilAcceso_id`,`formulario_id`),
  KEY `fk_perfilesFormularios_perfilAccesos1_idx` (`perfilAcceso_id`),
  KEY `fk_perfilesFormularios_formularios1_idx` (`formulario_id`),
  CONSTRAINT `fk_perfilesFormularios_formularios1` FOREIGN KEY (`formulario_id`) REFERENCES `formularios` (`id`),
  CONSTRAINT `fk_perfilesFormularios_perfilAccesos1` FOREIGN KEY (`perfilAcceso_id`) REFERENCES `perfilAccesos` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

/*
DROP TABLE `UtpIntegradorBuzzer`.`clientes`;
DROP TABLE `UtpIntegradorBuzzer`.`detalle_pedidos`;
DROP TABLE `UtpIntegradorBuzzer`.`formularios`;
DROP TABLE `UtpIntegradorBuzzer`.`historial_estados`;
DROP TABLE `UtpIntegradorBuzzer`.`locales`;
DROP TABLE `UtpIntegradorBuzzer`.`metricas`;
DROP TABLE `UtpIntegradorBuzzer`.`notificaciones`;
DROP TABLE `UtpIntegradorBuzzer`.`pedidos`;
DROP TABLE `UtpIntegradorBuzzer`.`perfilAccesos`;
DROP TABLE `UtpIntegradorBuzzer`.`perfilesFormularios`;
DROP TABLE `UtpIntegradorBuzzer`.`productos`;
DROP TABLE `UtpIntegradorBuzzer`.`restaurantes`;
DROP TABLE `UtpIntegradorBuzzer`.`trabajadores`;
*/