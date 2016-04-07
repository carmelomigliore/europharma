<?php
include_once('db.php');
include_once('agent.php');
$id=$_GET['id'];
$action = $_GET['action'];

if($action == 'spinner')
{
	$query=$db->prepare('SELECT DISTINCT annomese FROM storico ORDER BY annomese DESC'); // seleziona i prodotti
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<table>');
	echo('<form method="POST" action="index.php?section=statistiche&action=viewstats&id='.$id.'"><tr><td><select name="annomese">');
	foreach($annomese as $am){
		echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}
	echo('</select></td></tr><tr><td><input type="checkbox" name="byarea" value="true">Visualizza per area</td></tr><tr><td><input type="submit" value="Vedi statistiche" name="submit"/></td></tr></form>');
	echo('</table>');
}
else if($action == 'viewstats'){
	$annomese = $_POST['annomese'];
	$byarea = $_POST['byarea'];
	if($byarea==false){
		$query = $db->prepare('SELECT prodotti.nome, codarea as codice, aree.nome as area, numeropezzi FROM storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice ORDER BY prodotti.nome,area,codice');
		$query->execute(array(':idagente' => $id, ':annomese' => $annomese));
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$html = '<table border="1" width="50%"><tr><td>Prodotto</td><td>Zona</td><td>Numero pezzi</td></tr>';
		foreach($results as $row){
			$html.='<tr>';
			$html.='<td>'.$row['nome'].'</td><td>'.$row['area'].' '.substr($row['codice'],3).'</td><td style="text-align:right">'.$row['numeropezzi'].'</td>';
			$html.='</tr>';
		}
		$html.='</table>';
		echo($html);
	}else{
		$query = $db->prepare('SELECT prodotti.nome, codarea as codice, aree.nome as area, numeropezzi FROM storico, aree, prodotti WHERE idagente = :idagente AND annomese = :annomese AND storico.idprodotto = prodotti.id AND storico.codarea = aree.codice ORDER BY area,codice,prodotti.nome');
		$query->execute(array(':idagente' => $id, ':annomese' => $annomese));
		$results = $query->fetchAll(PDO::FETCH_ASSOC);
		$html = '<table border="1" width="50%"><tr><td>Zona</td><td>Prodotto</td><td>Numero pezzi</td></tr>';
		foreach($results as $row){
			$html.='<tr>';
			$html.='<td>'.$row['area'].' '.substr($row['codice'],3).'</td><td>'.$row['nome'].'</td><td style="text-align:right">'.$row['numeropezzi'].'</td>';
			$html.='</tr>';
		}
		$html.='</table>';
		echo($html);	
	}
}

?>
