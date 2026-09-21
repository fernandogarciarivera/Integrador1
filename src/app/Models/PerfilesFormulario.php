<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class PerfilesFormulario
 * 
 * @property int $id
 * @property int $perfilAcceso_id
 * @property int $formulario_id
 * 
 * @property Formulario $formulario
 * @property PerfilAcceso $perfil_acceso
 *
 * @package App\Models
 */
class PerfilesFormulario extends Model
{
	protected $table = 'perfilesFormularios';
	public $timestamps = false;

	protected $casts = [
		'perfilAcceso_id' => 'int',
		'formulario_id' => 'int'
	];

	public function formulario()
	{
		return $this->belongsTo(Formulario::class);
	}

	public function perfil_acceso()
	{
		return $this->belongsTo(PerfilAcceso::class);
	}
}
