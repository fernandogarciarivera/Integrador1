<?php

return [
    'estados' => config('estado_acceso.estados', ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO']),
    'finalizados' => config('estado_acceso.finalizados', ['ENTREGADO', 'CANCELADO']),
    'permisos' => config('estado_acceso.modulos.caja', [
        'default' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
    ]),
];
