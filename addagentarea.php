<?php
include('db.php');
include('agent.php');
$action=$_GET['action'];
$id=$_GET['id'];
if($action=='selectprovince'){
	$query = $db->prepare('SELECT DISTINCT nome FROM aree ORDER BY nome');
	$query->execute();
	$aree = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=addagentarea&action=selectmicro&id='.$id.'">');
	echo('<select name="nome">');
	foreach($aree as $area){
		echo('<option value="'.$area['nome'].'">'.$area['nome'].'</option>');
	}
	echo('</select><input type="submit" value="Invia"></form>');	
}else if($action=='selectmicro'){
	$nome=$_POST['nome'];
	$query = $db->prepare('SELECT nome, codice FROM aree WHERE nome = :nome AND codice NOT IN (SELECT area FROM "agente-aree" as aa WHERE aa.idagente = :id)');
	$query->execute(array(':nome' => $nome, ':id' => $id));
	$microaree = $query->fetchAll(PDO::FETCH_ASSOC);
	if(count($microaree)>0){
		echo('<form method="POST" action="index.php?section=addagentarea&action=insertmicro&id='.$id.'">');
		foreach($microaree as $microarea){
			echo($microarea['nome'].' '.substr($microarea['codice'],3).' <input type="checkbox" name="microaree[]" value="'.$microarea['codice'].'" checked><br>');
		}
		echo('<input type="submit" value="Invia"></form>');
	}else{
		echo('<br>Nessuna microarea disponibile per questa area <br><a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
	}
	
}
else if($action=='insertmicro'){
	$agente = Agent::getAgentFromDB($id,$db);
	$microaree = isset($_POST['microaree']) ? $_POST['microaree'] : array();
	foreach($microaree as $microarea){
		try{
		/*$query = $db->prepare('INSERT into "agente-aree"(area, idagente) VALUES (:idarea, :idagente)');
		$query->execute(array(':idarea' => $microarea, ':idagente' => $id));*/
		$agente->assignArea($db, $microarea);
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
}

else if($action=='deletemicro'){
	$agente = Agent::getAgentFromDB($id,$db);
	$microarea=$_GET['microarea'];
	try{
	$agente->deleteArea($db, $microarea);
	/*$query = $db->prepare('DELETE from "agente-aree" WHERE area = :idarea AND idagente = :idagente');
	$query->execute(array(':idarea' => $microarea, ':idagente' => $id));*/
	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
}


?>
