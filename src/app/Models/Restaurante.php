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
 * Class Restaurante
 * 
 * @property int $id
 * @property string $nombre
 * @property string|null $direccion
 * @property string|null $telefono
 * @property string|null $email
 * @property string $plan
 * @property string $estado
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Locale[] $locales
 * @property Collection|Metrica[] $metricas
 * @property Collection|Trabajadore[] $trabajadores
 *
 * @package App\Models
 */
class Restaurante extends Model
{
	use SoftDeletes;
	protected $table = 'restaurantes';

	protected $fillable = [
		'nombre',
		'direccion',
		'telefono',
		'email',
		'plan',
		'estado'
	];

	public function locales()
	{
		return $this->hasMany(Locale::class);
	}

	public function metricas()
	{
		return $this->hasMany(Metrica::class);
	}

	public function trabajadores()
	{
		return $this->hasMany(Trabajadore::class);
	}
}
