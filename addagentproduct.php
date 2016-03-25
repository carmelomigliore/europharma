<?php
include('db.php');
$action=$_GET['action'];
$id = $_GET['id'];
if($action=='selectproduct'){
	$query = $db->prepare('SELECT nome, id FROM prodotti WHERE id NOT IN (SELECT codprodotto FROM "agente-prodotto" WHERE idagente = :id)');
	$query->execute(array(':id' => $id));
	$prodotti=$query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=addagentproduct&action=selectaree&id='.$id.'">');
	echo('<select name="prodotto">');
	foreach($prodotti as $prodotto){
		echo('<option value="'.$prodotto['id'].'">'.$prodotto['nome'].'</option>');
	}
	echo('<br>Provvigione <input type="number" name="provvigione" required><br>');
	echo('<input type="submit" value="Invia"></form>');
}if($action=='selectaree'){
	$idprodotto = $_POST['prodotto'];
	$provvigione = $_POST['provvigione'];
	$query = $db->prepare('SELECT nome, area FROM aree, "agente-aree" as aa WHERE aa.idagente = :id AND aree.codice = aa.area AND aree.codice NOT IN (SELECT area FROM "agente-aree" as aa, "agente-prodotto" as ap, "agente-prodotto-area" as apa WHERE ap.codprodotto = :idprodotto AND ap.id = apa.idagenteprodotto AND aa.id = apa.idagentearea)');
	$query->execute(array(':id' => $id, ':idprodotto' => $idprodotto));
	$microaree = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=addagentproduct&action=insertproduct&id='.$id.'">');
	foreach($microaree as $microarea){
		echo($microarea['nome'].' '.$microarea['area'].' <input type="checkbox" name="microaree[]" value="'.$microarea['area'].'"><br>');
	}
	echo('<input name="prodotto" type="hidden" value="'.$idprodotto.'">');
	echo('<input name="provvigione" type="hidden" value="'.$provvigione.'">');
	echo('<input type="submit" value="Invia"></form>');
}
else if($action=='insertproduct'){
	$idprodotto = $_POST['prodotto'];
	$provvigione = $_POST['provvigione'];
	$microaree = $_POST['microaree'];
	$query = $db->prepare('INSERT INTO "agente-prodotto"(codprodotto, provvigione, idagente) VALUES (:codprodotto, :provvigione, :id)');
	$query->execute(array(':id' => $id, ':codprodotto' => $idprodotto, ':provvigione' => $provvigione));
	$query = $db->prepare('SELECT id FROM "agente-prodotto" WHERE codprodotto = :prodotto AND idagente = :id');
	$query->execute(array(':id' => $id, ':prodotto' => $idprodotto));
	$idagenteprodotto = $query->fetch();
	foreach($microaree as $microarea){
		$query = $db->prepare('SELECT id FROM "agente-aree" WHERE area = :microarea AND idagente = :id');
		$query->execute(array(':id' => $id, ':microarea' => $microarea));
		$idagentearea = $query->fetch();
		$query = $db->prepare('INSERT INTO "agente-prodotto-area" VALUES (:idagentearea, :idagenteprodotto)');
		$query->execute(array(':idagentearea' => $idagentearea[0], ':idagenteprodotto' => $idagenteprodotto[0]));
	}
}

else if($action=='deleteproduct'){
	$prod = $_GET['product'];
	try{
		$query = $db->prepare('DELETE from "agente-prodotto" WHERE idagente = :idagente AND codprodotto = :idprodotto');
		$query->execute(array(':idprodotto' => $prod, ':idagente' => $id));
		echo('Modifica avvenuta con successo'.$microarea.' '.$id);
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}


}
?>
