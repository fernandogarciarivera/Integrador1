<?php

return [
    'estados' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
    'proceso' => ['REGISTRADO', 'PREPARANDO', 'LISTO'],
    'finalizados' => ['ENTREGADO', 'CANCELADO'],

    'modulos' => [
        'caja' => [
            'SUPER_ADMIN' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'ADMIN_REST' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'GERENTE_LOCAL' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'CAJA' => ['REGISTRADO', 'CANCELADO'],
            'COCINA' => ['PREPARANDO', 'LISTO', 'CANCELADO'],
            'DESPACHO' => ['ENTREGADO', 'CANCELADO'],
            'default' => ['REGISTRADO', 'CANCELADO'],
        ],
        'cocina' => [
            'SUPER_ADMIN' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'ADMIN_REST' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'GERENTE_LOCAL' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'CAJA' => ['REGISTRADO', 'CANCELADO'],
            'COCINA' => ['PREPARANDO', 'LISTO', 'CANCELADO'],
            'DESPACHO' => ['ENTREGADO', 'CANCELADO'],
            'default' => ['REGISTRADO', 'CANCELADO'],
        ],
        'despacho' => [
            'SUPER_ADMIN' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'ADMIN_REST' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'GERENTE_LOCAL' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'CAJA' => ['REGISTRADO', 'CANCELADO'],
            'COCINA' => ['PREPARANDO', 'LISTO', 'CANCELADO'],
            'DESPACHO' => ['ENTREGADO', 'CANCELADO'],
            'default' => ['REGISTRADO', 'CANCELADO'],
        ],
        'administracion' => [
            'SUPER_ADMIN' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'ADMIN_REST' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'GERENTE_LOCAL' => ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
            'CAJA' => ['REGISTRADO', 'CANCELADO'],
            'COCINA' => ['PREPARANDO', 'LISTO', 'CANCELADO'],
            'DESPACHO' => ['ENTREGADO', 'CANCELADO'],
            'default' => ['REGISTRADO', 'CANCELADO'],
        ],
        'pos' => [
            'POS' => ['PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO'],
        ],
    ],
];
