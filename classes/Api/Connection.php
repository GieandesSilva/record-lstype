<?php

final class Connection {

	private function __construct() {}

	public static function open($name) {

		// Verifica se existe arquivo de configuração para este banco de dados

		if (file_exists("config/{$name}.ini")) {

			$db = parse_ini_file("config/{$name}.ini");
		}
		else {

			throw new Exception("Arquivo '$name' não encontrado");
		}

		// lê as informações contidas no arquivo

		$user = isset($db['user']) ? $db['user'] : NULL;
		$pass = isset($db['pass']) ? $db['pass'] : NULL;
		$name = isset($db['name']) ? $db['name'] : NULL;
		$host = isset($db['host']) ? $db['host'] : NULL;
		$type = isset($db['type']) ? $db['type'] : NULL;
		$port = isset($db['port']) ? $db['port'] : NULL;

		// descobre qual o tipo (driver) de banco de dados a ser utilizado

		switch($type) {

			case 'pgsql':
			$port = $port ? $port : '5432';
			$conn = new PDO("pgsql:dbname={$name}; user={$user}; password={$pass}; host=$host;port={$port}");
			break;

			case 'mysql':
			$port = $port ? $port : '3306';
			$conn = new PDO("mysql:host={$host};port={$port};dbname={$name}", $user, $pass);
			break;

			case 'sqlite':
			$conn = new PDO("sqlite:{$name}");
			break;

			case 'ibase':
			$conn = new PDO("firebird:dbname={$name}", $user, $pass);
			break;

			case 'oci8':
			$conn = new PDO("oci:dbname={$name}", $user, $pass);
			break;

			case 'mssql':
			$port = $port ? $port : '1433';
			$conn = new PDO("mssql:host={$host};port={$port};dbname={$name}", $user, $pass);
			break;
		}

		// Define para que o PDO lance Exceções na Ocorrência de erros.

		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		return $conn;
	}
}

?>