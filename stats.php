<?php
include_once('db.php');
include_once('agent.php');
$id=$_GET['id'];
$action = $_GET['action'];

if($action == 'spinner')
{
	$query=$db->prepare('SELECT DISTINCT annomese FROM ims ORDER BY annomese DESC'); // seleziona i prodotti
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=statistiche&action=viewstats&id='.$id.'"><select name="annomese">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select><input type="submit" value="Vedi statistiche" name="submit"/></form>');
}
else if($action == 'viewstats'){
	$annomese = $_POST['annomese'];
	$query = $db->prepare('SELECT prodotto, codice, area, numeropezzi FROM "monthly-results-agente-prodotto-microarea" WHERE idagente = :idagente AND annomese = :annomese ORDER BY prodotto');
	$query->execute(array(':idagente' => $id, ':annomese' => $annomese));
	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<table border="1"><tr><th>Prodotto</th><th>Zona</th><th>Numero pezzi</th></tr>');
	foreach($results as $row){
		echo('<tr>');
		echo('<td>'.$row['prodotto'].'</td><td>'.$row['area'].' '.substr($row['codice'],3).'</td><td style="text-align:right">'.$row['numeropezzi'].'</td>');
		echo('</tr>');
	}
	echo('</table>');
	
}

?>
