<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\FiltraPorAcceso;

/**
 * Class Pedido
 * 
 * @property int $id
 * @property int $local_id
 * @property int|null $cliente_id
 * @property int|null $trabajador_caja_id
 * @property string $codigo_pedido
 * @property string|null $codigo_qr
 * @property string|null $imagen_qr
 * @property Carbon|null $fecha_expira_qr
 * @property string $tipo
 * @property string $estado
 * @property Carbon $fecha_pedido
 * @property int|null $tiempo_preparacion_estimado
 * @property int|null $tiempo_preparacion_real
 * @property float|null $total
 * @property string|null $notas
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Cliente|null $cliente
 * @property Locale $locale
 * @property Trabajadore|null $trabajadore
 * @property Collection|DetallePedido[] $detalle_pedidos
 * @property Collection|HistorialEstado[] $historial_estados
 * @property Collection|Notificacione[] $notificaciones
 *
 * @package App\Models
 */
class Pedido extends Model
{
	use SoftDeletes, FiltraPorAcceso;

	protected $table = 'pedidos';

	protected $casts = [
		'local_id' => 'int',
		'cliente_id' => 'int',
		'trabajador_caja_id' => 'int',
		'fecha_expira_qr' => 'datetime',
		'fecha_pedido' => 'datetime',
		'tiempo_preparacion_estimado' => 'int',
		'tiempo_preparacion_real' => 'int',
		'total' => 'float'
	];

	protected $fillable = [
		'local_id',
		'cliente_id',
		'trabajador_caja_id',
		'codigo_pedido',
		'codigo_qr',
		'imagen_qr',
		'fecha_expira_qr',
		'tipo',
		'estado',
		'fecha_pedido',
		'tiempo_preparacion_estimado',
		'tiempo_preparacion_real',
		'total',
		'notas'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class);
	}

	public function locale()
	{
		return $this->belongsTo(Locale::class, 'local_id');
	}

	public function trabajadore()
	{
		return $this->belongsTo(Trabajadore::class, 'trabajador_caja_id');
	}

	public function detalle_pedidos()
	{
		return $this->hasMany(DetallePedido::class);
	}

	public function historial_estados()
	{
		return $this->hasMany(HistorialEstado::class);
	}

	public function notificaciones()
	{
		return $this->hasMany(Notificacione::class);
	}
}
