<?php

abstract class Record {

	protected $data;

	public function __construct($id = null) {

		if($id) { // se o ID for informado

			// Carrega o objeto correspondente

			$object = $this->load($id);

			if($object) {

				$this->fromArray($object->toArray());
			}
		}
	}

	public function __clone() {

		unset($this->data['id']);
	}

	public function __set($prop, $value) {

		if (method_exists($this, 'set_'.$prop)) {

			// Executa o método set_<propriedade>

			call_user_func(array($this, 'set_'.$prop), $value);
		} else {

			if ($value === NULL) {

				unset($this->data[$prop]);
			} else {

				$this->data[$prop] = $value; // Atribui o valor da propriedade
			}
		}
	}

	public function __get($prop) {

		if (method_exists($this, 'get_'.$prop)) {
			
			// Executa o método get_<propriedade>

			return call_user_func(array($this, 'get_'.$prop));
		} else {

			if (isset($this->data[$prop])) {

				return $this->data[$prop];
			}
		}
	}

	public function __isset($prop) {

		return isset($this->data[$prop]);
	}

	public function getEntity() {

		$class = get_class($this); // Obtém o nome da classe

		return constant("{$class}::TABLENAME"); // retorna a constante de classe TABLENAME
	}

	public function fromArray($data) {

		$this->data = $data;
	}

	public function toArray() {

		return $this->data;
	}

	public function store() {

		$prepared = self::prepare($this->data);
		// Incrementa o ID

		$this->id = self::getLast() +1;
		$prepared['id'] = $this->id;

			// Cria uma instrução de insert

		$sql = "INSERT INTO {$this->getEntity()} " . '(' . implode(', ', array_keys($prepared)) . ')' . 'values' . '(' . implode(', ', array_values($prepared)) . ')';

		// Obtém a transação ativa
		if ($conn = Transaction::get()) {

			Transaction::log($sql);
			$result = $conn->exec($sql);
			return $result;
		} else {

			throw new Exception("Não há transação ativa!!!");			
		}
	}

	public function load($id) {

		// Monta instrução de SELCT

		$sql = "SELECT * FROM {$this->getEntity()}";
		$sql .= ' WHERE id=' . (int) $id;

		
		// Obtém a transação ativa
		if ($conn = Transaction::get()) {

			// Cria mensagem de log e executa a consulta
			Transaction::log($sql);
			$result = $conn->query($sql);

			// Se retornou algum resultado
			if($result) {

				# Retorna os dados em forma de objeto
				$object = $result->fetchObject(get_class($this));
			}
			return $object;

		} else {

			throw new Exception("Não há transação ativa!!!");			
		}
	}

	public function delete($id = null) {

		// o ID é o parâmetro ou a propriedade ID

		$id = $id ? $id : $this->id;

		// Monta a string de DELETE

		$sql = "DELETE FROM {$this->getEntity()}";
		$sql .= ' WHERE id=' . (int) $this->data['id'];


		// Obtém a transação ativa
		if ($conn = Transaction::get()) {

			Transaction::log($sql);
			$result = $conn->exec($sql);
			return $result; // Retorna o resultado
		} else {

			throw new Exception("Não há transação ativa!!!");			
		}
	}

	public static function find($id) {

		$classname = get_called_class();
		$ar = new $classname;
		return $ar->load($id);
	}

	public function getLast() {

		if ($conn = Transaction::get()) {

			$sql = "SELECT max(id) FROM {$this->getEntity()}";

			// Cria o log e executa a instrução sql
			Transaction::log($sql);
			$result = $conn->query($sql);

			// Retorna os dados do banco
			$row = $result->fetch();
			return $row[0];
		} else {

			throw new Exception("Não há transação ativa!!!");
			
		}
	}

	public function prepare($data) {

		$prepared = [];

		foreach ($data as $key => $value) {
			
			if (is_scalar($value)) {

				$prepared[$key] = $this->escape($value);
			}
		}

		return $prepared;
	}

	public function escape($value) {

		if (is_string($value) and (!empty($value))) {

			// Adiciona \ em aspas
			$value = addslashes($value);
			return "'$value'";
		} else if (is_bool($value)){

			return $value ? 'TRUE' : 'FALSE';
		} else if ($value!=='') {

			return $value;
		} else {

			return "null";
		}
	}

	public function update($id) {

		$prepared = $this->prepare($this->data);

		// Verifica se tem o ID ou se existe na base de dados
		if (!empty($id) && (self::load($id))) {

		// Monta a string de UPDATE
			$sql = "UPDATE {$this->getEntity()} ";

			// Monta os pares: coluna=valor, ...

			if ($prepared) {

				foreach ($prepared as $column => $value) {

					if ($column !== 'id') {

						$set[] = "{$column} = {$value}";
					}
				}
			}

			$sql .= 'SET ' . implode(', ', $set);
			$sql .= ' WHERE id= ' . (int) $id;
			var_dump($sql);

			// Obtém a transação ativa
			if ($conn = Transaction::get()) {

				Transaction::log($sql);
				$result = $conn->exec($sql);
				return $result;
			} else {

				throw new Exception("Não há transação ativa!!!");			
			}
		}
	}
}