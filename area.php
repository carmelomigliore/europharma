<?php
include("util.php");
class Area {
	private $codice;
	private $nome;
	
	public function Area($codice, $microaree, $nome){
		$this->codice = $codice;
		$this->nome = $nome;
	}
	
	public function insertInDB($db){
		$query = $db->prepare("INSERT INTO aree VALUES (:codice, :nome)");
		$query->execute(array(':codice' => $codice, ':nome' => $nome));
	}
}
?>
