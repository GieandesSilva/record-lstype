<?php

class AppLoader {

	public function register() {

		spl_autoload_register(array($this, 'loadClass'));
	}

	public function loadClass($class) {

		if(file_exists("classes/App/{$class}.php")) {

			require_once "classes/App/{$class}.php";
			return TRUE;
		}
	}
}


?>