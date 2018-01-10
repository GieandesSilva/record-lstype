<?php

	abstract class Logger {

		protected $filename; // Local do arquivo de log

		public function __construct($filename) {

			$this->filename = $filename;
			file_put_contents($filename, ''); // limpa o conteúdo do Arquivo
		}

		//

		abstract function write($message);
	}