<?php

require_once __DIR__ . '/../config/environment.php';

class ControladorRuta{

	static public function ctrRuta(){

		return rtrim(app_env('APP_URL'), '/') . '/';

	}

	static public function ctrServidor(){

		return rtrim(app_env('BACKEND_URL'), '/') . '/';
	}

}