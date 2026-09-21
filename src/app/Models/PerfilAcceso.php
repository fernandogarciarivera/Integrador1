<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PerfilAcceso
 * 
 * @property int $id
 * @property string|null $perfil
 * 
 * @property Collection|PerfilesFormulario[] $perfiles_formularios
 *
 * @package App\Models
 */
class PerfilAcceso extends Model
{
	protected $table = 'perfilAccesos';
	public $timestamps = false;

	protected $fillable = [
		'perfil'
	];

	public function perfiles_formularios()
	{
		return $this->hasMany(PerfilesFormulario::class);
	}
}
