<?php
class Product {
	private $id;
	private $nome;
	private $prezzo;
	private $sconto;

	
	public function Product($id, $nome, $prezzo, $sconto){
		$this->id = $id;
		$this->nome = $nome;
		$this->prezzo = prezzo;
		$this->sconto = sconto;
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO prodotti(nome, sconto, prezzo) VALUES (:nome, :sconto, :prezzo) RETURNING id");
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$id = $result['id'];
	}
	
	public function updateIndDB($db){
		$query = $db->prepare('UPDATE prodotti SET nome = :nome, sconto = :sconto, prezzo = :prezzo WHERE id = :id');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':id' => $id));
	}
	
	

}


?>
