<?php

	require_once('autoload.php');
	$autoload = new Autoload();
	$autoload->register();

	try {

		Transaction::open('loja');
		Transaction::setLogger(new LoggerTXT('./tmp/log.txt'));
		Transaction::log('Inserindo Produto Novo.');

		$pro = new Produto;
		$pro->descricao = 'The Best Cake of the Aline Cook II';
		$pro->estoque = 21;
		$pro->preco_custo = 60;
		$pro->preco_venda = 99;
		$pro->codigo_barras = '1234567845670';
		$pro->data_cadastro = date('Y-m-d');
		$pro->origem = 'C';
		$pro->store();

		$pro2 = new Produto;
		$pro2->descricao = 'The Most Dangerous Coffe in the World II';
		$pro2->estoque = 21;
		$pro2->preco_custo = 30;
		$pro2->preco_venda = 111;
		$pro2->codigo_barras = '9876543209843';
		$pro2->data_cadastro = date('Y-m-d');
		$pro2->origem = 'C';
		$pro2->update(4);

		var_dump($pro2);

		Transaction::close();
		
	} catch (\Exception $e) {

		Transaction::rollback();
		print $e->getMessage();
	}

?>