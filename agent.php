<?php
class Agent {
	public $id;
	public $nome;
	public $cognome;
	public $codicefiscale;
	public $partitaiva;
	public $email;
	public $iva;
	public $enasarco;
	public $ritacconto;
	public $contributoinps;
	public $tipoinps;

	public function Agent($id, $nome, $cognome, $codicefiscale, $partitaiva, $email, $iva, $enasarco, $ritacconto, $contributoinps, $tipoinps){
		$this->nome = $nome;
		$this->cognome = $cognome;
		$this->codicefiscale = $codicefiscale;
		$this->partitaiva = $partitaiva;
		$this->email = $email;
		$this->iva = $iva;
		$this->enasarco = $enasarco;
		$this->ritacconto = $ritacconto;
		$this->contributoinps = $contributoinps;
		$this->tipoinps = $tipoinps;
		$this->id = $id;
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, tipoinps) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :tipoinps) RETURNING id");
		$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codicefiscale, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, 
		':enasarco' => $enasarco, ':ritacconto' => $ritacconto, ':contributoinps' => $contributoinps, ':tipoinps' => $tipoinps));
		$result = $query->fetch(PDO::FETCH_ASSOC);
		$id = $result['id'];
	}
	
	public function updateInDB($db){
		$query = $db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, tipoinps = :tipoinps WHERE id = :id');
		$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codicefiscale, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, 
		':enasarco' => $enasarco, ':ritacconto' => $ritacconto, ':contributoinps' => $contributoinps, ':tipoinps' => $tipoinps, ':id' => $id));
	}
	
	public function assignProduct($db, $product, $provvigione){
		$query = $db->prepare('INSERT INTO "agente-prodotto"(idagente, codprodotto, provvigione) VALUES (:idagente, :codprodotto, :provvigione)');
		$query->execute(array(':idagente' => $id, ':codprodotto' => $product->id, ':provvigione' => $provvigione));
	}
	
	public function assignArea($db, $codarea){
		$query = $db->prepare('INSERT INTO "agente-aree"(area, idagente) VALUES (:codarea, :idagente)');
		$query->execute(array(':codarea' => $codarea, ':idagente' => $id));
	}
	
	public function assignProductArea($db, $codprodotto, $codarea){
		$query = $db->prepare('SELECT id FROM "agente-aree" WHERE area = :codarea AND idagente = :idagente');
		$query->execute(array(':codarea' => $codarea, ':idagente' => $id));
		$resultagarea = $query->fetch(PDO::FETCH_ASSOC);
		$query = $db->prepare('SELECT id FROM "agente-prodotto" WHERE codprodotto = :codprodotto AND idagente = :idagente');
		$query->execute(array(':codprodotto' => $codprodotto, ':idagente' => $id));
		$resultagprod = $query->fetch(PDO::FETCH_ASSOC);
		$query = $db->prepare('SELECT insertagenteprodottoarea(:idagprod, :idagarea, :codprodotto, :codarea)');
		$query->execute(array(':idagprod' => $resultagprod['id'], ':idagarea' => $resultagarea['id'], ':codprodotto' => $codprodotto, ':codarea' => $codarea));
	}
	
}

?>
