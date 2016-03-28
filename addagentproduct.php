<?php
include('db.php');
include('agent.php');
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
	try{
		$idprodotto = $_POST['prodotto'];
		$provvigione = $_POST['provvigione'];
		$microaree = $_POST['microaree'];
		$agent = Agent::getAgentFromDB($id,$db);
		$agent->assignProduct($db,$idprodotto,$provvigione);
		/*$query = $db->prepare('INSERT INTO "agente-prodotto"(codprodotto, provvigione, idagente) VALUES (:codprodotto, :provvigione, :id)');
		$query->execute(array(':id' => $id, ':codprodotto' => $idprodotto, ':provvigione' => $provvigione));
		$query = $db->prepare('SELECT id FROM "agente-prodotto" WHERE codprodotto = :prodotto AND idagente = :id');
		$query->execute(array(':id' => $id, ':prodotto' => $idprodotto));
		$idagenteprodotto = $query->fetch();*/
		foreach($microaree as $microarea){
			/*$query = $db->prepare('SELECT id FROM "agente-aree" WHERE area = :microarea AND idagente = :id');
			$query->execute(array(':id' => $id, ':microarea' => $microarea));
			$idagentearea = $query->fetch();
			$query = $db->prepare('INSERT INTO "agente-prodotto-area" VALUES (:idagentearea, :idagenteprodotto)');
			$query->execute(array(':idagentearea' => $idagentearea[0], ':idagenteprodotto' => $idagenteprodotto[0]));*/
			$agent->assignProductArea($db, $idprodotto, $microarea);
		}
		echo('Prodotto assegnato con successo');
	} catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}

else if($action=='deleteproduct'){
	$prod = $_GET['product'];
	$agent = Agent::getAgentFromDB($id,$db);
	try{
		/*$query = $db->prepare('DELETE from "agente-prodotto" WHERE idagente = :idagente AND codprodotto = :idprodotto');
		$query->execute(array(':idprodotto' => $prod, ':idagente' => $id));*/
		$agent->deleteProduct($db, $prod);
		echo('Modifica avvenuta con successo');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}

else if($action=='deleteareaproduct'){
	$idagenteprodottoarea = $_GET['idareaproduct'];
	$agent = Agent::getAgentFromDB($id,$db);
	try{
		/*$query = $db->prepare('DELETE from "agente-prodotto" WHERE idagente = :idagente AND codprodotto = :idprodotto');
		$query->execute(array(':idprodotto' => $prod, ':idagente' => $id));*/
		$agent->deleteProductArea($db, $idagenteprodottoarea);
		echo('Modifica avvenuta con successo');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}


}

else if($action == 'addareaproduct'){

	$idprodotto = $_GET['product'];
	$query = $db->prepare('WITH myquery AS ((SELECT aree.nome, area FROM aree, "agente-aree" as aa WHERE aa.idagente = :id AND aree.codice = aa.area AND aree.codice NOT IN (SELECT area FROM "agente-aree" as aa, "agente-prodotto" as ap, "agente-prodotto-area" as apa WHERE ap.codprodotto = :idprodotto AND ap.id = apa.idagenteprodotto AND aa.id = apa.idagentearea)) EXCEPT SELECT aree.nome, area from "agente-aree" as aa,aree, "agente-prodotto" as ap, "agente-prodotto-area" as apa  WHERE apa.idagentearea = aa.id AND apa.idagenteprodotto = ap.id AND aree.codice = aa.area AND aa.idagente = :id AND ap.idagente = aa.idagente AND  ap.codprodotto = :idprodotto) SELECT * FROM myquery ORDER BY nome, area'); 
	/*$query = $db->prepare('SELECT aree.nome, area from "agente-aree" as aa,aree, "agente-prodotto" as ap, "agente-prodotto-area" as apa  WHERE apa.idagentearea = aa.id AND apa.idagenteprodotto = ap.id AND aree.codice = aa.area AND aa.idagente = :id AND ap.idagente = aa.idagente AND  ap.codprodotto = :idprodotto'); */
	$query->execute(array(':id' => $id, ':idprodotto' => $idprodotto));
	$microaree = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=addagentproduct&action=insertareaproduct&id='.$id.'">');
	foreach($microaree as $microarea){
		echo($microarea['nome'].' '.$microarea['area'].' <input type="checkbox" name="microaree[]" value="'.$microarea['area'].'"><br>');
	}
	echo('<input name="prodotto" type="hidden" value="'.$idprodotto.'">');
	echo('<input type="submit" value="Invia"></form>');
}

else if($action == 'insertareaproduct'){
	$idprodotto = $_POST['prodotto'];
	$agent = Agent::getAgentFromDB($id,$db);
	$microaree = $_POST['microaree'];
	try{
	foreach($microaree as $microarea){
			$agent->assignProductArea($db, $idprodotto, $microarea);
		}
		echo('Aree aggiornate con successo');
	} catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}

else if($action=='addallproducts'){

		$query1 = $db->prepare('SELECT * FROM "agente-aree" WHERE idagente = :id');
		$query1->execute(array(':id' => $id));
		$aree=$query1->fetchAll(PDO::FETCH_ASSOC);
		$query = $db->prepare('SELECT * FROM prodotti');
		$query->execute();
		$prodotti=$query->fetchAll(PDO::FETCH_ASSOC);
		foreach($prodotti as $prodotto){
			//echo('<option value="'.$prodotto['id'].'">'.$prodotto['nome'].'</option>');
			$agent = Agent::getAgentFromDB($id,$db);
			$agent->assignProduct($db,$prodotto['id'],$prodotto['provvigionedefault']);
			$counter = 0;
			foreach($aree as $area){
				try{
					$agent->assignProductArea($db, $prodotto['id'], $area['area']);
					$counter++;
				}catch(Exception $pdoe){
					//echo('Errore: '.$pdoe->getMessage());
					continue;
				}
			}
		if($counter == 0){
			$agent->deleteProduct($db,$prodotto['id']);

		}

			

		
	}
	echo('Operazione eseguita con successo');
}

?>
