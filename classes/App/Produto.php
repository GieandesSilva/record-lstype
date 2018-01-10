<<?php 

class Produto {

	private $data;


	public function __set($prop, $value) {

		$this->data[$prop] = $value;
	}

	public function __get($prop) {

		return $this->data[$prop];
	}

	public function save() {

		$sql = "INSERT INTO estoque.produto (descricao,	estoque, preco_custo, preco_venda, codigo_barras, data_cadastro, origem)" . 
		" VALUES ('{$this->descricao}', " .
		"'{$this->estoque}', " .
		"'{$this->preco_custo}'," .
		"'{$this->preco_venda}'," .
		"'{$this->codigo_barras}'," .
		"'{$this->data_cadastro}'," .
		"'{$this->origem}')";
		$conn = Transaction::get();
		Transaction::log($sql);
		return $conn->exec($sql);
	}

	public static function all($filter = '') {

		$sql = "SELECT * FROM estoque.produto";

		if($filter) {

			$sql .= "where $filter";
		}
		$conn = Transaction::get();
		Transaction::log($sql);
		$result = $conn->query($sql);
		return $result->fetchAll(PDO::FETCH_CLASS, __CLASS__);
	}

	public static function find($id) {

		$sql = "SELECT * FROM estoque.produto where id = '$id' ";
		$conn = Transaction::get();
		Transaction::log($sql);
		$result = $conn->query($sql);
		return $result->fetchObject(__CLASS__);;
	}

	public function delete() {

		$sql = "DELETE FROM estoque.produto where id = '$this->id' ";
		$conn = Transaction::get();
		Transaction::log($sql);
		return $conn->query($sql);
	}

	public function update($id) {

		$sql = "UPDATE estoque.produto SET descricao = '{$this->descricao}', " . " estoque = '{$this->estoque}', " . " preco_custo = '{$this->preco_custo}', " . " preco_venda = '{$this->preco_venda}', " . " codigo_barras = '{$this->codigo_barras}', " . " data_cadastro = '{$this->data_cadastro}', " . " origem = '{$this->origem}' " . " WHERE id = '{$id}'";
		$conn = Transaction::get();
		Transaction::log($sql);
		return $conn->exec($sql);
	}

	public function getMargemLucro() {

		return (($this->preco_venda - $this->preco_custo) / $this->preco_custo) * 100;
	}

	public function registraCompra($custo, $quantidade) {

		$this->custo = $custo;
		$this->estoque += $quantidade;
	}
}

?>