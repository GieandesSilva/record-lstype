<?php

class LoggerXML extends Logger {

	public function write($message) {

		date_default_timezone_set('America/Sao_Paulo');
		$time = date("Y-m-d H:i:s");

		$text = "<content>\n";
		$text.= "<info>\n";
		$text.= "	<time>$time</time>\n";
		$text.= "	<message>$message</message>\n";
		$text.= "</info>\n";

		// Adiciona ao final do arquivo

		$handler = fopen($this->filename, 'a');

		fwrite($handler , utf8_encode($text));
		fclose($handler);
	}
}