<?php

namespace Tests\Unit;

use App\Models\DetallePedido;
use Tests\TestCase;

class CajaDetallePedidoCompatibilityTest extends TestCase
{
    public function test_detalle_pedido_accepts_database_column_and_caja_alias(): void
    {
        $detalle = new DetallePedido([
            'pedido_id' => 1,
            'detalleProducto' => 'Combo especial',
            'productoDesc' => 'Combo especial',
            'cantidad' => 2,
            'precio_unitario' => 12.50,
            'subtotal' => 25.00,
        ]);

        $this->assertSame('Combo especial', $detalle->detalleProducto);
        $this->assertSame('Combo especial', $detalle->productoDesc);
    }
}
