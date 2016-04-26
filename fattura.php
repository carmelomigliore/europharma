<?php
include_once('db.php');
include_once('agent.php');
//include('./vsword/VsWord.php');
define('EURO',chr(128));
$id=$_GET['id'];
$action = $_GET['action'];
/*$query=$db->prepare('SELECT * FROM agenti WHERE id = :id');
$query->execute(array(':id' => $id));
$row = $query->fetch(PDO::FETCH_ASSOC);*/
$agente = Agent::getAgentFromDB($id,$db);
if($action == 'spinner')
{
	$query=$db->prepare('SELECT DISTINCT annomese FROM ims ORDER BY annomese DESC LIMIT 12'); // seleziona l'anno'
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<div class="caricodati" align="center" style="width:300px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Genera Fatture</p></h1>
		</div>');

	echo('<form method="POST" action="index.php?section=fattura&action=generafattura&id='.$id.'">
		<br>Testo positivo: <input type="text" name="textpositivo">
		<br>Valore positivo: <input type="number" step="any" name="valuepositivo">
		<br>Testo negativo: <input type="text" name="textnegativo">
		<br>Valore negativo <input type="number" step="any" name="valuenegativo">');
	echo('<select name="anno">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select><input type="submit" value="Genera Fattura IMS"/></form>');
	
	$query=$db->prepare('SELECT DISTINCT annomese FROM farmacie ORDER BY annomese DESC LIMIT 12'); // seleziona l'anno'
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);

	echo('</div></div>');
	
	echo('<br><br><form method="GET" action="index.php"><input type="hidden" name="section" value="fattura"><input type="hidden" name="action" value="selectfatturafarmacie"><input type="hidden" name="id" value="'.$id.'">');
	echo('<select name="anno">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select><input type="submit" value="Fatture Farmacia"/></form>');
}

if($action == 'generafattura')
{
	$annomese = $_POST['anno'];
	$textpositivo=$_POST['textpositivo']!=''?$_POST['textpositivo']:null;
	$valuepositivo=$_POST['valuepositivo']!=''?$_POST['valuepositivo']:null;
	$textnegativo=$_POST['textnegativo']!=''?$_POST['textnegativo']:null;
	$valuenegativo=$_POST['valuenegativo']!=''?$_POST['valuenegativo']:null;
	

	$agente->calculateIMS($db, $annomese, $textpositivo, $valuepositivo, $textnegativo, $valuenegativo);
	
	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
}

if($action == 'selectfatturafarmacie'){
	$annomese = $_GET['anno'];
	$query=$db->prepare('SELECT numerofattura, farmacia FROM "compensi-farmacie" WHERE idagente = :idagente AND annomese = :annomese GROUP BY numerofattura, farmacia');
	$query->execute(array(':annomese' => $annomese, ':idagente' => $id));
	$fatture = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<div class="caricodati" align="center" style="width:400px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Crea Fatture</p></h1>
		</div>');	
	
	echo('<form method="GET" action="index.php"><input type="hidden" name="section" value="fattura"><input type="hidden" name="action" value="generafatturafarmacia"><input type="hidden" name="id" value="'.$id.'"><input type="hidden" name="annomese" value="'.$annomese.'">');
	echo('<select name="numerofattura">');
	foreach($fatture as $row){
		echo('<option value="'.$row['numerofattura'].'">'.$row['numerofattura'].' '.$row['farmacia'].'</option>');
	}
	echo('</select><input type="submit" value="Genera Fattura Farmacia"/></form>');
	echo('</div></div>');
}

if($action == 'generafatturafarmacia')
{
	$annomese = $_GET['annomese'];
	$numerofattura = $_GET['numerofattura'];
	$agente->calculateFarmacia($db, $annomese, $numerofattura);
	
	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
}

	
?>
