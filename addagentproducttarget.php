<?php
include_once('db.php');
include_once('agent.php');
include_once('util.php');

$action=$_GET['action'];
$id = $_GET['id'];
$idagenteprodotto = $_GET['idagenteprodotto'];

if($action=='settarget'){
	$query = $db->prepare('SELECT prodotti.nome FROM prodotti, "agente-prodotto" as ap WHERE ap.id = :idagenteprodotto AND ap.codprodotto = prodotti.id');
	$query->execute(array(':idagenteprodotto' => $idagenteprodotto));
	$productname = $query->fetch();
	$productname = $productname[0];
	
	$query2 = $db->prepare('SELECT prodotti.nome, ap.id as idagenteprodotto FROM prodotti, "agente-prodotto" as ap WHERE ap.idagente = :idagente AND ap.id <> :idagenteprodotto AND ap.codprodotto = prodotti.id');
	$query2->execute(array(':idagenteprodotto' => $idagenteprodotto, ':idagente' => $id));
	$otherproducts = $query2->fetchAll(PDO::FETCH_ASSOC);
	
	echo('<p>Aggiungi target per il prodotto '.$productname);
	echo('<form method="POST" action="index.php?section=addagentproducttarget&action=inserttarget&id='.$id.'&idagenteprodotto='.$idagenteprodotto.'">');
	echo('<table>');
	echo('<tr><td>Target pezzi </td><td><input type="number" min="0" name="target" required></td></tr>');
	echo('<tr><td>Provvigione bonus </td><td><input type="number" step="any" min="0" name="provvigione" required></td></tr>');
	$index=0;
	foreach($otherproducts as $prod){
		if($index%4 == 0){
			echo('<tr>');
		}
		echo('<td>'.$prod['nome'].' <input type="checkbox" name="products[]" value="'.$prod['idagenteprodotto'].'"></td>');
		if($index%4 == 0){
			echo('</tr>');
		}
		$index++;
	}
	if($index%4 != 1 && $index!=0){
		echo('</tr>');
	}
	
	echo('<tr><td></td><td><input type="submit" name="Invia"></td></tr>');
}
else if($action=='inserttarget'){
	$products = isset($_POST['products']) ? array_merge($_POST['products'], array($idagenteprodotto)) : array($idagenteprodotto);
	$target = $_POST['target'];
	$provvigione = $_POST['provvigione'];
	try{
		$query = $db->prepare('SELECT insertarget(:products, :target, :percentuale)');	
		$query->execute(array(':products' => php_to_postgres_array($products), ':target' => $target, ':percentuale' => $provvigione));
		echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}
else if($action=='deletetarget'){
	$idtarget=$_GET['idtarget'];
	try{
		$query = $db->prepare('DELETE from "agente-prodotto-target" WHERE id = :idtarget');	
		$query->execute(array(':idtarget' => $idtarget));
		echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}


?>
