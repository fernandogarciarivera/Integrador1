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
 * Class Producto
 * 
 * @property int $id
 * @property int $local_id
 * @property string $nombre
 * @property string|null $descripcion
 * @property float $precio
 * @property string|null $categoria
 * @property bool $disponible
 * @property string|null $url_imagen
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Locale $locale
 * @property Collection|DetallePedido[] $detalle_pedidos
 *
 * @package App\Models
 */
class Producto extends Model
{
	use SoftDeletes;
	protected $table = 'productos';

	protected $casts = [
		'local_id' => 'int',
		'precio' => 'float',
		'disponible' => 'bool'
	];

	protected $fillable = [
		'local_id',
		'nombre',
		'descripcion',
		'precio',
		'categoria',
		'disponible',
		'url_imagen'
	];

	public function locale()
	{
		return $this->belongsTo(Locale::class, 'local_id');
	}

	public function detalle_pedidos()
	{
		return $this->hasMany(DetallePedido::class);
	}
}
