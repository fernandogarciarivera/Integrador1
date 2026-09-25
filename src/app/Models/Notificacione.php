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
 * Class Notificacione
 * 
 * @property int $id
 * @property int $pedido_id
 * @property int|null $cliente_id
 * @property string $tipo
 * @property string $mensaje
 * @property Carbon $fecha_envio
 * @property Carbon|null $fecha_lectura
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Cliente|null $cliente
 * @property Pedido $pedido
 *
 * @package App\Models
 */
class Notificacione extends Model
{

	use SoftDeletes, FiltraPorAcceso;

	protected $table = 'notificaciones';

	protected $casts = [
		'pedido_id' => 'int',
		'cliente_id' => 'int',
		'fecha_envio' => 'datetime',
		'fecha_lectura' => 'datetime'
	];

	protected $fillable = [
		'pedido_id',
		'cliente_id',
		'tipo',
		'mensaje',
		'fecha_envio',
		'fecha_lectura',
		'estado'
	];

	public function cliente()
	{
		return $this->belongsTo(Cliente::class);
	}

	public function pedido()
	{
		return $this->belongsTo(Pedido::class);
	}
}
