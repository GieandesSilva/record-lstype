<?php

	final class Transaction {

		private static $conn;
		private static $logger;

		private function __construct() {}

		public static function open($database) {

			if(empty(self::$conn)) {

				self::$conn = Connection::open($database);
				self::$conn->beginTransaction(); // Inicia a Transação
				self::$logger = NULL; // Desliga o Log de SQL
			}
		}

		public static function get() {

			return self::$conn; // retorna conexão
		}

		public static function rollback() {

			if (self::$conn) {

				self::$conn->rollback(); // Desfaz as operações realizadas
				self::$conn = NULL; 
			}
		}

		public static function close() {

			if(self::$conn) {

				self::$conn->commit(); // Aplica as operações realizadas
				self::$conn = NULL;
			}
		}

		public static function setLogger(Logger $logger) {

			self::$logger = $logger;
		}

		public static function log($message) {

			if(self::$logger) {
				self::$logger->write($message);
			}
		}
	}

?>