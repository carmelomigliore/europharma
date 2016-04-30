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
		<br>	<h1><p>Genera Fatture IMS</p></h1>
		</div>');

	echo('<form method="POST" action="index.php?section=fattura&action=generafattura&id='.$id.'">
		<br>Testo positivo: <input type="text" name="textpositivo">
		<br>Valore positivo: <input type="number" step="any" name="valuepositivo">
		<br>Testo negativo: <input type="text" name="textnegativo">
		<br>Valore negativo <input type="number" step="any" name="valuenegativo">');
	echo('<br>Anno e mese: <select name="anno">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select><br><input type="submit" value="Genera Fattura IMS"/></form>');
	
	/*$query=$db->prepare('SELECT DISTINCT annomese FROM farmacie ORDER BY annomese DESC LIMIT 12'); // seleziona l'anno'
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);*/

	echo('</div></div><br><br>');
	echo('<div class="caricodati" align="center" style="width:300px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Fatture Farmacie</p></h1>
		</div>');
	echo('<br><br><form method="GET" action="index.php"><input type="hidden" name="section" value="fattura"><input type="hidden" name="action" value="selectfatturafarmacie"><input type="hidden" name="id" value="'.$id.'">');
	echo('</select><input type="submit" value="Fatture Farmacia"/></form>');
	echo('</div></div><br><br>');
	echo('<div class="caricodati" align="center" style="width:300px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Fatture Libere</p></h1>
		</div>');
	echo('<form method="POST" action="index.php?section=fattura&action=generafatturalibera&id='.$id.'">
		<br>Tipo di fattura: <input type="text" name="tipo" required>
		<br>Imponibile: <input type="number" step="any" name="imponibile" required>');
	echo('<br>Anno e mese: <select name="anno">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select><br><input type="submit" value="Genera Fattura Libera"/></form>');
	echo('</div></div>');
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

if($action == 'generafatturalibera')
{
	$annomese = $_POST['anno'];
	$tipo=$_POST['tipo'];
	$imponibile=$_POST['imponibile'];
	
	try{
	
		$agente->calculateNettoPrintFattura($db, $imponibile, str_replace(' ', '', $tipo), $annomese, null,null,null,null,$tipo);
	
		$query=$db->prepare("INSERT INTO storicoftlibere VALUES ($id, $annomese, $imponibile)");
		$query->execute();
	
		echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
	} catch(Exception $e){
		echo $e->getMessage();
	}
}

if($action == 'selectfatturafarmacie'){
	//$annomese = $_GET['anno'];
	$query=$db->prepare('SELECT annomese, numerofattura, farmacia FROM "compensi-farmacie" WHERE idagente = :idagente AND liquidato = \'\' GROUP BY numerofattura, farmacia, annomese');
	$query->execute(array(':idagente' => $id));
	$fatture = $query->fetchAll(PDO::FETCH_ASSOC);
	
	$query=$db->prepare('SELECT id, nome, cognome FROM agenti WHERE attivo = TRUE AND tipoattivita=\'CapoArea\' ORDER BY cognome');
	$query->execute();
	$capiarea = $query->fetchAll(PDO::FETCH_ASSOC);
	
	echo('<div class="caricodati" align="center" style="width:400px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Liquida Fatture Farmacie</p></h1>
		</div>');	
	
	echo('<form method="POST" action="index.php?section=fattura&action=generafatturafarmacia&id='.$id.'">');
	foreach($fatture as $row){
		echo('Capo area: <select name="capoarea">');
		echo('<option value="-1">-</option>');
		foreach($capiarea as $capoarea){
			echo('<option value="'.$capoarea['id'].'">'.$capoarea['cognome'].' '.$capoarea['nome'].'</option>');
		}
		echo('</select><br>');
		echo('<input type="checkbox" name="farmacie[]" value="'.$row['annomese'].'_'.$row['numerofattura'].'">'.$row['annomese'].' - Numero: '.$row['numerofattura'].' - Farmacia: '.$row['farmacia']);
		echo('<br>');
	}
	echo('<input type="submit" value="Liquida Farmacie"/></form>');
	echo('</div></div>');
}

if($action == 'generafatturafarmacia')
{
	$fatture = isset($_POST['farmacie']) ? $_POST['farmacie'] : array();
	$idcapoarea = $_POST['capoarea'];
	$capoarea=null;
	if($idcapoarea!=-1){
		$capoarea = Agent::getAgentFromDB($idcapoarea,$db);
	}
	$imponibile = 0;
	$imponibilecapo = 0;
	$query=$db->prepare('UPDATE farmacie SET liquidato = :mesecorrente WHERE annomese = :annomese AND idagente = :idagente AND numerofattura = :numerofattura');
	
	try{
		foreach($fatture as $fattura){
			$values = explode('_',$fattura);
			$imponibile+=$agente->calculateFarmacia($db, $values[0], $values[1]);
			$query->execute(array(':idagente' => $id, ':annomese' => $values[0], ':numerofattura' => $values[1], ':mesecorrente' => date('Y').date('m')));
			if(!is_null($capoarea)){
				//$querystoricocapoarea->execute(array(':idcapoarea' => $idcapoarea, ':annomese' => $values[0], ':numerofattura' => $values[1]));
				$imponibilecapo += $capoarea->calculateFarmaciaCapo($db, $values[0], $values[1]);
			}
		}
	}catch(Exception $e){
		echo $e->getMessage().' Line: '.$e->getLine();
	}
	$agente->calculateNettoPrintFattura($db, $imponibile, 'farmacie', date('Y').date('m'));
	if(!is_null($capoarea)){
		$capoarea->calculateNettoPrintFattura($db, $imponibilecapo, 'farmacie_capoarea', date('Y').date('m'));
	}
	
	
	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=viewagent&id='.$id.'">Torna indietro</a>');
}

	
?>
