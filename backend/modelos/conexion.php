<?php

require_once __DIR__ . '/../../config/environment.php';

Class Conexion{

	static public function conectar(){

		$link = new PDO("mysql:host=" . app_env('DB_HOST') . ";dbname=" . app_env('DB_NAME'),
						app_env('DB_USER'),
						app_env('DB_PASSWORD'));

		$link->exec("set names utf8");

		return $link;

	}


}