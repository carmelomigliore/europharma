<?php
include('db.php');
include('agent.php');
$action=$_GET['action'];
if($action=='uploadims'){
	
	$query = $db->prepare("SELECT id FROM prodotti WHERE nome = :nome");
	$query2 = $db->prepare("INSERT INTO ims(annomese, idprodotto, numeropezzi, idarea) VALUES(:annomese, :idprodotto, :numeropezzi, :idarea)");
	$userfile_tmp = $_FILES['userfile']['tmp_name'];
	$file = file_get_contents($userfile_tmp);
	$lines = explode("\r\n",$file);   //controllare se line feed o carriage return
	$counter = 0;
	foreach($lines as $line){
		try{
			//echo($line.'<br>');
			$values = explode(";",$line);
			if(count($values) < 5)
				continue;
			//echo($values[2].'<br>');
			$query->execute(array(':nome' => $values[2]));
			$idprodotto = $query->fetch();
			$idprodotto = $idprodotto[0];
			if(!is_numeric($idprodotto)){
				echo('Il prodotto '.$values[2].' è stato skippato perché non presente nel database <br>');
				continue;
			}
			$query2->execute(array(':annomese' => $values[0], ':idarea' => $values[1], ':idprodotto' => $idprodotto, ':numeropezzi' => $values[3]));
			$counter++;
		}catch(Exception $pdoe){
			//echo('Errore: '.$pdoe->getMessage().'<br>');
			continue;
		}
	}
	echo('Operazione eseguita con successo, inserite '.$counter.' righe <a href="index.php?section=agenti">Torna indietro</a>');
		
} else if($action=='uploadfarmacie'){

	$query = $db->prepare("SELECT id FROM prodotti WHERE nome = :nome");
	$query2 = $db->prepare("INSERT INTO farmacie(annomese, idprodotto, numeropezzi, idagente,farmacia,numerofattura) VALUES(:annomese, :idprodotto, :numeropezzi, :idagente, :farmacia, :numerofattura)");
	$query3 = $db->prepare("SELECT id FROM agenti WHERE codicefiscale = :codicefiscale");
	$userfile_tmp = $_FILES['userfile']['tmp_name'];
	$file = file_get_contents($userfile_tmp);
	$lines = explode("\r\n",$file);   //controllare se line feed o carriage return
	$counter = 0;
	$annomese = $_POST['selection'];
	foreach($lines as $line){
		try{
			//echo($line.'<br>');
			$values = explode(";",$line);
			if(count($values) < 5)
				continue;
			//echo($values[2].'<br>');
			$query->execute(array(':nome' => $values[1]));
			$idprodotto = $query->fetch();
			$idprodotto = $idprodotto[0];
			if(!is_numeric($idprodotto)){
				echo('Il prodotto '.$values[1].' è stato skippato perché non presente nel database <br>');
				continue;
			}
			$query3->execute(array(':codicefiscale' => strtoupper($values[0])));
			$idagente = $query3->fetch();
			$idagente = $idagente[0];
			if(is_null($idagente)){
				echo('Il collaboratore con codice fiscale '.$values[0].' è stato skippato perché non presente nel database <br>');
				continue;
			}
			$query2->execute(array(':annomese' => $annomese, ':idagente' => $idagente, ':idprodotto' => $idprodotto, ':numeropezzi' => $values[2], ':farmacia' => $values[3], ':numerofattura' => $values[4]));
			$counter++;
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage().'<br>');
			continue;
		}
	}
	echo('Operazione eseguita, inserite '.$counter.' righe <a href="index.php?section=agenti">Torna indietro</a>');
}
else if($action=='annullaims'){
	$annomese = $_POST['selection'];
	try{
		$query= $db->prepare('DELETE FROM ims WHERE annomese = :annomese');
		$query->execute(array(':annomese' => $annomese));
		echo('Operazione eseguita con successo <a href="index.php?section=agenti">Torna indietro</a>');
	}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage().'<br><a href="index.php?section=agenti">Torna indietro</a>');
	}	
}
else if($action=='annullafarmacie'){
	$annomese = $_POST['selection'];
	try{
		$query= $db->prepare('DELETE FROM farmacie WHERE annomese = :annomese');
		$query->execute(array(':annomese' => $annomese));
		echo('Operazione eseguita con successo <a href="index.php?section=agenti">Torna indietro</a>');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage().'<br><a href="index.php?section=agenti">Torna indietro</a>');
	}	
}

else if($action=='salvastorico'){
	$annomese = $_POST['selection'];
	try{
		$query = $db->prepare('WITH myquery AS (SELECT micro.idagente, micro.annomese, micro.codice, micro.numeropezzi, provv.provvigione, (prezzo - prezzo*sconto/100) AS prezzonetto, micro.codprodotto FROM "monthly-results-agente-prodotto-microarea" AS micro, "monthly-results-agente-prodotto-provvigione" AS provv, prodotti  WHERE micro.annomese = :annomese AND micro.idagente = provv.idagente AND micro.codprodotto = provv.codprodotto AND micro.codprodotto = prodotti.id) INSERT INTO storico SELECT * FROM myquery');
		$query->execute(array(':annomese' => $annomese));
		
		$query = $db->prepare('SELECT id FROM agenti WHERE attivo = TRUE');
		$query->execute();
		$ids = $query->fetchAll(PDO::FETCH_ASSOC);
		foreach($ids as $id){
			$agent = Agent::getAgentFromDB($id['id'],$db);
			$agent->calculateIMS($db, $annomese);
		}
		echo('Operazione eseguita con successo <a href="index.php?section=agenti">Torna indietro</a>');
	}catch(Exception $pdoe){
		echo('Errore: '.$pdoe->getMessage().'<br><a href="index.php?section=agenti">Torna indietro</a>');
	}
	
}
else if($action=='calcolo'){
	$annomese = $_POST['selection'];
	$query = $db->prepare('SELECT v.nome, v.cognome, codicefiscale, v.importolordo, v.idagente FROM "monthly-results-agente-importolordo" AS v, agenti WHERE annomese = :annomese AND v.idagente = agenti.id AND attivo = TRUE ORDER BY v.cognome');
	$query->execute(array(':annomese' => $annomese));
	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<p><a href="index.php?section=agenti">Torna indietro</a></p>');
	echo('<table border="1">');
	echo('<tr><td>Nome</td><td>Cognome</td><td>Codice fiscale</td><td>Imponibile calcolato in euro</td><td></td></tr>');
	foreach ($results as $row){
		echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td style="text-align:right">'.$row['importolordo'].'</td><td><a href="index.php?section=viewagent&id='.$row['idagente'].'">dettagli</a></td></tr>');
	}
	echo('</table>');
	
}

else{
echo('<div class="caricodati">');
echo('<p>Carico dati IMS</p>');
	echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=uploadims" method="POST">
<table>
<tr>
<td>
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
</td>
<td>
  File dati IMS:</td> 

<td>
<input name="userfile" type="file"></br></td>
  <br><td><input type="submit" value="Invia File"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');

$query=$db->prepare('SELECT DISTINCT annomese FROM ims ORDER BY annomese DESC'); // seleziona l'anno'
$query->execute();
$annomese = $query->fetchAll(PDO::FETCH_ASSOC);
echo('<div class="caricodati">');
echo('<p>Carico dati FARMACIE</p>');
echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=uploadfarmacie" method="POST">
<table>');
echo('<tr><td>Seleziona l\'anno e il mese</td><td><select name="selection">');
foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select></td></tr>');
echo('<tr>
  <input type="hidden" name="MAX_FILE_SIZE" value="30000">
<td>File dati FARMACIE:</td> 
<td>
<input name="userfile" type="file"></br></td>
  <br><td><input type="submit" value="Invia File"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');

echo('<div class="caricodati">');
echo('<p>Visualizza calcolo imponibile di ogni agente</p>');
echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=calcolo" method="POST">
<table>');
echo('<tr><td>Seleziona l\'anno e il mese</td><td><select name="selection">');
foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select></td></tr>');
echo('<tr><td><input type="submit" value="Calcolo"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');

echo('<div class="caricodati">');
echo('<p>Annulla importazione dati IMS</p>');
echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=annullaims" method="POST">
<table>');
echo('<tr><td>Seleziona l\'anno e il mese</td><td><select name="selection">');
foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select></td></tr>');
echo('<tr><td><input type="submit" value="Annulla importazione"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');

$query=$db->prepare('SELECT DISTINCT annomese FROM farmacie ORDER BY annomese DESC'); // seleziona l'anno'
$query->execute();
$annomese = $query->fetchAll(PDO::FETCH_ASSOC);

echo('<div class="caricodati">');
echo('<p>Annulla importazione dati FARMACIE</p>');
echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=annullafarmacie" method="POST">
<table>');
echo('<tr><td>Seleziona l\'anno e il mese</td><td><select name="selection">');
foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select></td></tr>');
echo('<tr><td><input type="submit" value="Annulla importazione"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');

$query=$db->prepare('WITH myquery AS (SELECT DISTINCT annomese FROM ims EXCEPT SELECT DISTINCT annomese FROM storico) SELECT annomese FROM myquery ORDER BY annomese DESC'); // seleziona l'anno'
$query->execute();
$annomese = $query->fetchAll(PDO::FETCH_ASSOC);

echo('<div class="caricodati">');
echo('<p>Conferma dati e salva nello storico</p>');
echo('<form enctype="multipart/form-data" action="index.php?section=caricodati&action=salvastorico" method="POST">
<table>');
echo('<tr><td>Seleziona l\'anno e il mese</td><td><select name="selection">');
foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select></td></tr>');
echo('<tr><td><input type="submit" value="Salva nello storico"></td>
</tr>
</table>
</form>');
echo('</div><br><br><br>');
}

?>
