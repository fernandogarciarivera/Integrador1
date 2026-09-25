<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\FiltraPorAcceso;

/**
 * Class HistorialEstado
 * 
 * @property int $id
 * @property int $pedido_id
 * @property int|null $trabajador_id
 * @property string|null $estado_anterior
 * @property string $estado_nuevo
 * @property Carbon $fecha_cambio
 * @property string|null $observaciones
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Pedido $pedido
 * @property Trabajadore|null $trabajadore
 *
 * @package App\Models
 */
class HistorialEstado extends Model
{
	use SoftDeletes, FiltraPorAcceso;

	protected $table = 'historial_estados';

	protected $casts = [
		'pedido_id' => 'int',
		'trabajador_id' => 'int',
		'fecha_cambio' => 'datetime'
	];

	protected $fillable = [
		'pedido_id',
		'trabajador_id',
		'estado_anterior',
		'estado_nuevo',
		'fecha_cambio',
		'observaciones'
	];

	public function pedido()
	{
		return $this->belongsTo(Pedido::class);
	}

	public function trabajadore()
	{
		return $this->belongsTo(Trabajadore::class, 'trabajador_id');
	}
}
