<?php
include('db.php');
include('agent.php');
$action = $_GET['action'];
$id=-1;
$nome='';
$sconto='';
$prezzo='';
$provdefault='';
$target1 = 0;
$target2 = 0;
$target3 = 0;
$percentuale1 = 0;
$percentuale2 = 0;
$percentuale3 = 0;

if($action=='mod'){
	$id = $_GET['id'];
	$query = $db->prepare('SELECT * FROM prodotti WHERE id = :id');
	$query->execute(array(':id' => $id));
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$nome=$result['nome'];
	$sconto = $result['sconto'];
	$prezzo = $result['prezzo'];
	$provdefault= $result['provvigionedefault'];
	$target1 = $result['target1'];
	$target2 = $result['target2'];
	$target3 = $result['target3'];
	$percentuale1 = $result['percentuale1'];
	$percentuale2 = $result['percentuale2'];
	$percentuale3 = $result['percentuale3'];
}

if($action == 'add' || $action == 'mod'){
	if($action=='add')
		echo('<form method="POST" action="index.php?section=insertproduct&action=insert">');
	else
		echo('<form method="POST" action="index.php?section=insertproduct&action=update&id='.$id.'">');
	
	echo('Nome: <input type="text" name="nome" value="'.$nome.'" required="required"><br>');
	echo('Sconto: <input type="number" name="sconto" step="any" min="0" value="'.$sconto.'" required="required"><br>');
	echo('Prezzo: <input type="number" name="prezzo" step="any" min="0" value="'.$prezzo.'" required="required"><br>');
	echo('Provvigione default: <input type="number" step="any" min="0" name="provvigionedefault" value="'.$provdefault.'" required="required"><br>');
	echo('Target default 1: <input type="number" name="target1" min="0" value="'.$target1.'"><br>');
	echo('Percentuale target 1: <input type="number" step="any" name="percentuale1" min="0" value="'.$percentuale1.'"><br>');
	echo('Target default 2: <input type="number" name="target2" min="0" value="'.$target2.'"><br>');
	echo('Percentuale target 2: <input type="number" step="any" name="percentuale2" min="0" value="'.$percentuale2.'"><br>');
	echo('Target default 3: <input type="number" name="target3" min="0" value="'.$target3.'"><br>');
	echo('Percentuale target 3: <input type="number" step="any" name="percentuale3" min="0" value="'.$percentuale3.'"><br>');
	if($action=='add')
		echo('<input type="checkbox" name="addagents" value="true">Aggiungi a tutti gli agenti<br>');
	echo('ATTENZIONE!! È possibile inserire solamente target di default che non prevedano la somma con altri prodotti.<br>');
	echo('<input type="submit" name="Invia">');
	echo('</form>');
}

if($action=='insert' || $action=='update'){
	$nome= $_POST['nome'];
	$sconto=$_POST['sconto'];
	$prezzo = $_POST['prezzo'];
	$provdefault=$_POST['provvigionedefault'];
	$target1 = $_POST['target1'];
	$target2 = $_POST['target2'];
	$target3 = $_POST['target3'];
	$percentuale1 = $_POST['percentuale1'];
	$percentuale2 = $_POST['percentuale2'];
	$percentuale3 = $_POST['percentuale3'];
	$addagents = $_POST['addagents'];
	
	if($action=='insert'){	
		try{
			$query=$db->prepare('INSERT into prodotti (nome,sconto,prezzo,provvigionedefault, target1, percentuale1, target2, percentuale2, target3, percentuale3) VALUES (:nome, :sconto, :prezzo, :provvigionedefault, :target1, :percentuale1, :target2, :percentuale2, :target3, :percentuale3) RETURNING id');
			$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':provvigionedefault' => $provdefault, ':target1'=> $target1, ':percentuale1' => $percentuale1, ':target2'=> $target2, ':percentuale2' => $percentuale2, ':target3'=> $target3, ':percentuale3' => $percentuale3));
			$idprod = $query->fetch();
			$idprod = $idprod[0];
			
			if($addagents == true){
				$query = $db->prepare('SELECT id FROM agenti ORDER BY cognome');	
				$query->execute();
				$idagents = $query->fetchAll(PDO::FETCH_ASSOC);
				
				foreach($idagents as $idagent){
					$agente = Agent::getAgentFromDB($idagent['id'],$db);
					$agente->assignProduct($db,$idprod,$provdefault);
					$counter = 0;
					//echo('disa'.$nome);
					$query1 = $db->prepare('SELECT * FROM "agente-aree" WHERE idagente = :id');
					$query1->execute(array(':id' => $idagent['id']));
					$aree=$query1->fetchAll(PDO::FETCH_ASSOC);
					
					foreach($aree as $area){
						try{
							$agente->assignProductArea($db, $idprod, $area['area']);
							$counter++;
						}
						catch(Exception $pdoe){
							//echo('Errore: '.$pdoe->getMessage());
							continue;
						}
					
					}
					if($counter == 0){
						$agente->deleteProduct($db,$idprod);
						echo('<br>Prodotto non assegnabile al collaboratore '.$agente->nome.' '.$agente->cognome);
						
					}	
				}
			}
			echo('<br>Operazione eseguita con successo <br><a href="index.php?section=prodotti">Torna indietro</a>');
		
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
		
	}
	else if($action=='update'){
		try{
		$id = $_GET['id'];
		$query=$db->prepare('UPDATE prodotti SET nome = :nome, prezzo = :prezzo, sconto = :sconto, provvigionedefault = :provvigionedefault, target1 = :target1, percentuale1 = :percentuale1, target2 = :target2, percentuale2 = :percentuale2, target3 = :target3, percentuale3 = :percentuale3 WHERE id = :id');
		$query->execute(array(':nome' => $nome, ':sconto' => $sconto, ':prezzo' => $prezzo, ':id' => $id,':provvigionedefault' => $provdefault, ':target1'=> $target1, ':percentuale1' => $percentuale1, ':target2'=> $target2, ':percentuale2' => $percentuale2, ':target3'=> $target3, ':percentuale3' => $percentuale3));
		echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=prodotti">Torna indietro</a>');
		}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage());
	}
	}
}		
?>
