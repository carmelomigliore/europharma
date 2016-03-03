<?php

include('db.php');
$action = $_GET['action'];
$id=-1;
$nome='';
$sconto='';
$prezzo='';

if($action=='mod'){
	$id = $_GET['id'];
	$query = $db->prepare('SELECT * FROM prodotti WHERE id = :id');
	$query->execute(array(':id' => $id));
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$nome=$result['nome'];
	$sconto = $result['sconto'];
	$prezzo = $result['prezzo'];
}

if($action == 'add' || $action == 'mod'){
	if($action=='add')
		echo('<form method="POST" action="index.php?section=addproduct&action=insert">');
	else
		echo('<form method="POST" action="index.php?section=addproduct&action=update&id='.$id.'">');
	
	echo('Nome: <input type="text" name="nome" value="'.$nome.'" required="required"><br>');
	echo('Sconto: <input type="number" name="sconto" value="'.$sconto.'" required="required">');
	echo('Prezzo: <input type="number" name="prezzo" value="'.$prezzo.'" required="required">');
	echo('<input type="submit" name="Invia">');
	echo('</form>');
}

if($action=='insert' || $action=='update'){
	$nome= $_POST['nome'];
	$sconto=$_POST['sconto'];
	$prezzo = $_POST['prezzo'];
	
	if($action=='insert'){
		$query=$db->prepare('INSERT into prodotti (nome,sconto,prezzo) VALUES (:nome, :sconto, :prezzo)');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo));
	}
	else if($action=='update'){
		$query=$db->prepare('UPDATE prodotti SET nome = :nome, prezzo = :prezzo, sconto = :sconto WHERE id = :id');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':id' => $id));
	}
}		
?>
