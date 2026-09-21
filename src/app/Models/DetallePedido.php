<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class DetallePedido
 * 
 * @property int $id
 * @property int $pedido_id
 * @property int $producto_id
 * @property int $cantidad
 * @property float $precio_unitario
 * @property float $subtotal
 * @property string|null $instrucciones_especiales
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Pedido $pedido
 * @property Producto $producto
 *
 * @package App\Models
 */
class DetallePedido extends Model
{
	protected $table = 'detalle_pedidos';

	protected $casts = [
		'pedido_id' => 'int',
		'producto_id' => 'int',
		'cantidad' => 'int',
		'precio_unitario' => 'float',
		'subtotal' => 'float'
	];

	protected $fillable = [
		'pedido_id',
		'producto_id',
		'cantidad',
		'precio_unitario',
		'subtotal',
		'instrucciones_especiales'
	];

	public function pedido()
	{
		return $this->belongsTo(Pedido::class);
	}

	public function producto()
	{
		return $this->belongsTo(Producto::class);
	}
}
