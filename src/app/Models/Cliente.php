<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Cliente
 * 
 * @property int $id
 * @property string|null $nombre
 * @property string|null $telefono
 * @property string|null $email
 * @property array|null $preferencias_notificacion
 * @property Carbon|null $fecha_ultima_visita
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * 
 * @property Collection|Notificacione[] $notificaciones
 * @property Collection|Pedido[] $pedidos
 *
 * @package App\Models
 */
class Cliente extends Model
{
	protected $table = 'clientes';

	protected $casts = [
		'preferencias_notificacion' => 'json',
		'fecha_ultima_visita' => 'datetime'
	];

	protected $fillable = [
		'nombre',
		'telefono',
		'email',
		'preferencias_notificacion',
		'fecha_ultima_visita'
	];

	public function notificaciones()
	{
		return $this->hasMany(Notificacione::class);
	}

	public function pedidos()
	{
		return $this->hasMany(Pedido::class);
	}
}
