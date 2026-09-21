<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Metrica
 * 
 * @property int $id
 * @property int $restaurante_id
 * @property int $local_id
 * @property Carbon $fecha
 * @property int $total_pedidos
 * @property float|null $tiempo_promedio_espera
 * @property float|null $porcentaje_notificaciones_exitosas
 * @property int $pedidos_gestionados_qr
 * @property int $clientes_unicos
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Restaurante $restaurante
 * @property Locale $locale
 *
 * @package App\Models
 */
class Metrica extends Model
{
	protected $table = 'metricas';

	protected $casts = [
		'restaurante_id' => 'int',
		'local_id' => 'int',
		'fecha' => 'datetime',
		'total_pedidos' => 'int',
		'tiempo_promedio_espera' => 'float',
		'porcentaje_notificaciones_exitosas' => 'float',
		'pedidos_gestionados_qr' => 'int',
		'clientes_unicos' => 'int'
	];

	protected $fillable = [
		'restaurante_id',
		'local_id',
		'fecha',
		'total_pedidos',
		'tiempo_promedio_espera',
		'porcentaje_notificaciones_exitosas',
		'pedidos_gestionados_qr',
		'clientes_unicos'
	];

	public function restaurante()
	{
		return $this->belongsTo(Restaurante::class);
	}

	public function locale()
	{
		return $this->belongsTo(Locale::class, 'local_id');
	}
}
