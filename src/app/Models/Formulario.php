<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Formulario
 * 
 * @property int $id
 * @property string $formulario
 * @property string|null $controller
 * 
 * @property Collection|PerfilesFormulario[] $perfiles_formularios
 *
 * @package App\Models
 */
class Formulario extends Model
{
	protected $table = 'formularios';
	public $timestamps = false;

	protected $fillable = [
		'formulario',
		'controller'
	];

	public function perfiles_formularios()
	{
		return $this->hasMany(PerfilesFormulario::class);
	}
}
