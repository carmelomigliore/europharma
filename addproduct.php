<?php

include('db.php');
$action = $_GET['action'];
$id=-1;
$nome='';
$sconto='';
$prezzo='';
$provdefault='';

if($action=='mod'){
	$id = $_GET['id'];
	$query = $db->prepare('SELECT * FROM prodotti WHERE id = :id');
	$query->execute(array(':id' => $id));
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$nome=$result['nome'];
	$sconto = $result['sconto'];
	$prezzo = $result['prezzo'];
	$provdefault= $result['provvigionedefault'];
}

if($action == 'add' || $action == 'mod'){
	if($action=='add')
		echo('<form method="POST" action="index.php?section=insertproduct&action=insert">');
	else
		echo('<form method="POST" action="index.php?section=insertproduct&action=update&id='.$id.'">');
	
	echo('Nome: <input type="text" name="nome" value="'.$nome.'" required="required"><br>');
	echo('Sconto: <input type="number" name="sconto" step="any" min="0" value="'.$sconto.'" required="required"><br>');
	echo('Prezzo: <input type="number" name="prezzo" step="any" min="0" value="'.$prezzo.'" required="required"><br>');
	echo('Provvigione default: <input type="number" step="any" min="0" name="provvigionedefault" value="'.$provdefault.'" required="required">');
	echo('<input type="submit" name="Invia">');
	echo('</form>');
}

if($action=='insert' || $action=='update'){
	$nome= $_POST['nome'];
	$sconto=$_POST['sconto'];
	$prezzo = $_POST['prezzo'];
	$provdefault=$_POST['provvigionedefault'];
	
	
	if($action=='insert'){	
		try{
		echo('disa'.$nome);
		$query=$db->prepare('INSERT into prodotti (nome,sconto,prezzo,provvigionedefault) VALUES (:nome, :sconto, :prezzo, :provvigionedefault)');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':provvigionedefault' => $provdefault));
		echo('Prodotto aggiunto');
		}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
	}
	else if($action=='update'){
		try{
		$id = $_GET['id'];
		$query=$db->prepare('UPDATE prodotti SET nome = :nome, prezzo = :prezzo, sconto = :sconto, provvigionedefault = :provvigionedefault WHERE id = :id');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':id' => $id,':provvigionedefault' => $provdefault));
		echo('Prodotto aggiornato');
		}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
	}
}		
?>
