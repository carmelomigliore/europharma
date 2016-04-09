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
	$agente = Agent::getAgentFromDB($id, $db);
	$annomese = $_POST['annomese'];
	$byarea = $_POST['byarea']; 
	if($byarea==false){
		$results = $agente->statsNormal($db, $annomese);
		$html = '<table border="1" width="50%"><tr><th>Prodotto</th><th>Zona</th><th>Numero pezzi</th><th>Provvigione</th><th>Prezzo netto</th><th>Spettanza</th></tr>';
		foreach($results as $row){
			$html.='<tr>';
			$html.='<td>'.$row['nome'].'</td><td>'.$row['area'].'</td><td style="text-align:right">'.$row['numeropezzi'].'</td><td style="text-align:right">'.$row['provvigione'].'</td><td style="text-align:right">'.number_format($row['prezzonetto'],2,',','.').'</td><td style="text-align:right">'.number_format($row['spettanza'],2,',','.').'</td>';
			$html.='</tr>';
		}
		$html.='</table>';
		echo($html);
		$agente->generateCSV($results, array('Prodotto','Microarea','Numero Pezzi', 'Provvigione', 'Prezzo Netto', 'Spettanza'), 'stats', $annomese);	
		
	}else{
		$columns = array();
		$results = $agente->statsPivot($db, $annomese,$columns);
		$html = '<table border="1" ><tr>';
		
		foreach ($columns as $column){
			$html.='<th>'.str_replace('_', ' ', strtoupper($column)).'</th>';	
		}
		$html.='</tr>';

		foreach($results as $row){
			$html.='<tr>';
			foreach($row as $value){
				$html.='<td>'.$value.'</td>';
			}
			$html.='</tr>';

		}
		$html.='</table>';
		//$file = 'byarea.csv';
		echo($html);	
		$agente->generateCSV($results, $columns, 'pivot', $annomese);
		//file_put_contents($file, $string);
	}
}

?>
