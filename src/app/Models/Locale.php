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
 * Class Locale
 * 
 * @property int $id
 * @property int $restaurante_id
 * @property string $nombre
 * @property string|null $direccion
 * @property string|null $codigo
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Restaurante $restaurante
 * @property Collection|Metrica[] $metricas
 * @property Collection|Pedido[] $pedidos
 * @property Collection|Producto[] $productos
 * @property Collection|Trabajadore[] $trabajadores
 *
 * @package App\Models
 */
class Locale extends Model
{
	use SoftDeletes;
	protected $table = 'locales';

	protected $casts = [
		'restaurante_id' => 'int'
	];

	protected $fillable = [
		'restaurante_id',
		'nombre',
		'direccion',
		'codigo',
		'estado'
	];

	public function restaurante()
	{
		return $this->belongsTo(Restaurante::class);
	}

	public function metricas()
	{
		return $this->hasMany(Metrica::class, 'local_id');
	}

	public function pedidos()
	{
		return $this->hasMany(Pedido::class, 'local_id');
	}

	public function productos()
	{
		return $this->hasMany(Producto::class, 'local_id');
	}

	public function trabajadores()
	{
		return $this->hasMany(Trabajadore::class, 'local_id');
	}
}
