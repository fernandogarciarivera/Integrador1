<?php

namespace App\Support;

class EstadoAccesoPedido
{
    public static function permisosPorPerfil(?string $perfil, ?string $modulo = 'caja'): array
    {
        $config = config('estado_acceso_pedido.modulos.' . ($modulo ?? 'caja'), []);
        $perfil = $perfil ?: 'default';

        if (isset($config[$perfil])) {
            return $config[$perfil];
        }

        return $config['default'] ?? config('estado_acceso_pedido.estados', ['REGISTRADO', 'PREPARANDO', 'LISTO', 'ENTREGADO', 'CANCELADO']);
    }

    public static function puedeAsignar(string $perfil, string $estado, ?string $modulo = 'caja'): bool
    {
        return in_array($estado, self::permisosPorPerfil($perfil, $modulo), true);
    }

    public static function pedidoFinalizado(string $estado, ?string $modulo = 'caja'): bool
    {
        $finalizados = config('estado_acceso_pedido.modulos.' . ($modulo ?? 'caja') . '.finalizados', config('estado_acceso_pedido.finalizados', ['ENTREGADO', 'CANCELADO']));

        return in_array($estado, $finalizados, true);
    }

    public static function puedeMoverEstado(string $perfil, string $estadoActual, string $estadoDestino, ?string $modulo = 'caja'): bool
    {
        if (self::pedidoFinalizado($estadoActual, $modulo)) {
            return false;
        }

        return self::puedeAsignar($perfil, $estadoDestino, $modulo);
    }
}
