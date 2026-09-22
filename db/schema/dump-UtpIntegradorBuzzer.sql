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
  -- UNIQUE KEY `uk_locales_restaurante_codigo` (`restaurante_id`,`codigo`),
  -- UNIQUE KEY `uk_locales_restaurante_codigo` (`restaurante_id`),
  KEY `idx_locales_restaurante` (`restaurante_id`),
  CONSTRAINT `fk_locales_restaurante` FOREIGN KEY (`restaurante_id`) REFERENCES `restaurantes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

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

DROP TABLE IF EXISTS `trabajadores`;
CREATE TABLE `trabajadores` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `restaurante_id` bigint unsigned NOT NULL,
  `local_id` bigint unsigned DEFAULT NULL,
  `rol` enum('SUPER_ADMIN','ADMIN_REST','GERENTE_LOCAL','CAJA','COCINA','DESPACHO') NOT NULL,
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

DROP TABLE IF EXISTS `restaurantTmp`;
CREATE TABLE `restaurantTmp` (
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
  KEY `idx_restaurantTmp_estado` (`estado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `restaurantTmp` (`nombre`, `direccion`, `telefono`, `email`, `plan`, `estado`, `created_at`, `updated_at`) VALUES
  ('La Lucha Sanguchería Criolla - Larco', 'Av. José Larco 999, Miraflores, Lima', '014421111', 'contacto@lalucha.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('La Lucha Sanguchería Criolla - Diagonal', 'Av. Diagonal 308, Miraflores, Lima', '014421112', 'diagonal@lalucha.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Sanguchería El Chinito - Centro', 'Jr. Chancay 894, Cercado de Lima, Lima', '014232190', 'pedidos@elchinito.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Sanguchería El Chinito - Surco', 'Av. Caminos del Inca 1151, Santiago de Surco, Lima', '012423000', 'surco@elchinito.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Tip Top - Lince', 'Av. Gral. Juan Antonio Álvarez de Arenales 2499, Lince, Lima', '014713131', 'ventas@tiptop.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Pardos Chicken - Aeropuerto', 'Av. Elmer Faucett s/n (Aeropuerto Internacional Jorge Chávez), Callao', '015173100', 'aeropuerto@pardoschicken.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Bembos - Larco', 'Av. José Larco 401, Miraflores, Lima', '014191919', 'servicioalcliente@bembos.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Bembos - Jockey Plaza', 'Av. Javier Prado Este 4200 (CC Jockey Plaza), Santiago de Surco, Lima', '014191920', 'jockey@bembos.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('China Wok - Plaza San Miguel', 'Av. Universitaria 2000 (CC Plaza San Miguel), San Miguel, Lima', '016128000', 'contacto@chinawok.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Norky\'s - Centro', 'Av. Abancay 601, Cercado de Lima, Lima', '014284444', 'contacto@norkys.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Roky\'s - Angamos', 'Av. Angamos Este 1502, Surquillo, Lima', '016135000', 'servicio@rokys.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Siete Sopas - Angamos', 'Av. Angamos Este 609, Surquillo, Lima', '012136000', 'informes@sietesopas.com.pe', 'ENTERPRISE', 'ACTIVO', NOW(), NOW()),
  ('Siete Sopas - Lince', 'Av. Arequipa 2394, Lince, Lima', '012136001', 'lince@sietesopas.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Sándwiches Monstruos - Barranco', 'Av. de la Aviación 3005, San Borja, Lima', '014761022', 'contacto@monstruos.com.pe', 'BASICO', 'ACTIVO', NOW(), NOW()),
  ('Juicy Lucy - La Mar', 'Av. Mariscal La Mar 1328, Miraflores, Lima', '014411234', 'info@juicylucy.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Papacho\'s - Larcomar', 'Malecón de la Reserva 610 (CC Larcomar), Miraflores, Lima', '014467000', 'larcomar@papachos.com', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Pasquale Hermanos - Megaplaza', 'Av. Alfredo Mendiola 3698 (CC MegaPlaza), Los Olivos, Lima', '015116000', 'contacto@pasquale.com.pe', 'BASICO', 'ACTIVO', NOW(), NOW()),
  ('D\'Onofrio Heladería - Centro', 'Jr. de la Unión 500, Cercado de Lima, Lima', '014260000', 'helados@donofrio.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('La Caravana - Canaval y Moreyra', 'Av. Canaval y Moreyra 501, San Isidro, Lima', '014413030', 'informes@lacaravana.com.pe', 'PRO', 'ACTIVO', NOW(), NOW()),
  ('Begui - Luna Pizarro', 'Av. Luna Pizarro 415, La Victoria, Lima', '014721050', 'contacto@begui.com.pe', 'BASICO', 'ACTIVO', NOW(), NOW());

INSERT INTO `restaurantes` (`nombre`, `direccion`, `telefono`, `email`, `plan`, `estado`)
SELECT TRIM(SUBSTRING_INDEX(nombre, '-', 1)) AS nombre, direccion, telefono, email, plan, estado FROM UtpIntegradorBuzzer.restaurantTmp 
WHERE id NOT IN (7,1,4,13) order by nombre;

INSERT INTO `locales` (`restaurante_id`, `nombre`, `direccion`, `codigo`, `estado`, `created_at`, `updated_at`)
SELECT rst.id, TRIM(SUBSTRING_INDEX(lcl.nombre, '-', -1)) AS nombre, lcl.direccion, '', 'ACTIVO', NOW(), NOW() FROM restaurantes AS rst INNER JOIN restaurantTmp AS lcl ON TRIM(SUBSTRING_INDEX(lcl.nombre, '-', 1)) = rst.nombre;

DROP TABLE `UtpIntegradorBuzzer`.`restaurantTmp`;

INSERT INTO `UtpIntegradorBuzzer`.`perfilAccesos` (`perfil`) VALUES 
  ('SUPER_ADMIN'), 
  ('ADMIN_REST'), 
  ('GERENTE_LOCAL'), 
  ('CAJA'), 
  ('COCINA'), 
  ('DESPACHO');

INSERT INTO `UtpIntegradorBuzzer`.`formularios` (`formulario`, `controller`) VALUES 
  ('Configuraciones', 'ConfiguracionController'), 
  ('Dashboard', 'DashboardController'), 
  ('Pedidos Caja', 'PedidosController'), 
  ('Pedidos Cocina', 'LocalesController'),
  ('Pedidos Despacho', 'LocalesController'),
  ('Reporte', 'ReporteController'), 
  ('Trabajadores', 'TrabajadoresController'), 
  ('Empresa Contrata', 'RestaurantController'), 
  ('Locales', 'LocalesController');

INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 1 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm;
INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 2 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm WHERE frm.id NOT IN (3,4,5);
INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 3 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm WHERE frm.id NOT IN (3,4,5);
INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 4 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm WHERE frm.id IN (3);
INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 5 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm WHERE frm.id IN (4,5);
INSERT INTO `UtpIntegradorBuzzer`.`perfilesFormularios` (`perfilAcceso_id`, `formulario_id`) SELECT 6 as 'perfil_id', frm.id FROM `UtpIntegradorBuzzer`.`formularios` as frm WHERE frm.id IN (5);



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