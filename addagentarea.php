<?php
include('db.php');
$action=$_GET['action'];
$id=$_GET['id'];
if($action=='selectprovince'){
	$query = $db->prepare('SELECT DISTINCT nome FROM aree');
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
	$query = $db->prepare('SELECT codice FROM aree WHERE nome = :nome AND codice NOT IN (SELECT area FROM "agente-aree" as aa WHERE aa.idagente = :id)');
	$query->execute(array(':nome' => $nome, ':id' => $id));
	$microaree = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=addagentarea&action=insertmicro&id='.$id.'">');
	foreach($microaree as $microarea){
		echo($microarea['codice'].' <input type="checkbox" name="microaree[]" value="'.$microarea['codice'].'"><br>');
	}
	echo('<input type="submit" value="Invia"></form>');
	
}
else if($action=='insertmicro'){
	$microaree = isset($_POST['microaree']) ? $_POST['microaree'] : array();
	foreach($microaree as $microarea){
		$query = $db->prepare('INSERT into "agente-aree"(area, idagente) VALUES (:idarea, :idagente)');
		$query->execute(array(':idarea' => $microarea, ':idagente' => $id));
	}

}

else if($action=='deletemicro')
	{
		$microarea=$_GET['microarea'];
		try{
		$query = $db->prepare('DELETE from "agente-aree" WHERE area = :idarea AND idagente = :idagente');
		$query->execute(array(':idarea' => $microarea, ':idagente' => $id));
		echo('Modifica avvenuta con successo'.$microarea.' '.$id);
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}


?>
