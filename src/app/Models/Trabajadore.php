<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Trabajadore
 * 
 * @property int $id
 * @property int $user_id
 * @property int $restaurante_id
 * @property int|null $local_id
 * @property string $rol
 * @property string|null $puesto
 * @property string|null $telefono
 * @property bool $activo
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Locale|null $locale
 * @property Restaurante $restaurante
 * @property User $user
 * @property Collection|HistorialEstado[] $historial_estados
 * @property Collection|Pedido[] $pedidos
 *
 * @package App\Models
 */
class Trabajadore extends Model
{
	use SoftDeletes;
	protected $table = 'trabajadores';

	protected $casts = [
		'user_id' => 'int',
		'restaurante_id' => 'int',
		'local_id' => 'int',
		'activo' => 'bool'
	];

	protected $fillable = [
		'user_id',
		'restaurante_id',
		'local_id',
		'rol',
		'puesto',
		'telefono',
		'activo'
	];

	public function locale()
	{
		return $this->belongsTo(Locale::class, 'local_id');
	}

	public function restaurante()
	{
		return $this->belongsTo(Restaurante::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function historial_estados()
	{
		return $this->hasMany(HistorialEstado::class, 'trabajador_id');
	}

	public function pedidos()
	{
		return $this->hasMany(Pedido::class, 'trabajador_caja_id');
	}
}
