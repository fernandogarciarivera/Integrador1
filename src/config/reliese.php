<?php
return [
	'connection'	=>	'mysql',
	'models_path'	=>	app_path('Models'),
	'ignore_tables'	=>	[
		'users','password_reset_tokens','sessions','cache','cache_locks','jobs','job_batches','failed_jobs','migrations','formularios','perfilAccesos','perfilesFormularios',
	],
	'timestamp'	=>	true,
	'soft_deletes'	=>	true,
	'nullable'	=>	true,
	'snake_attributes'	=>	false,
];