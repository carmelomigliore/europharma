<?php
include('db.php');
$action = $_GET['action'];

if($action == 'esportaanagrafica'){
	$sql = 'SELECT * FROM agenti WHERE attivo = TRUE';
	$query = $db->prepare($sql);
	$query->execute();
	$results = $query->fetchAll(PDO::FETCH_NUM);
	$columns = array();
	$rs = $db->query('WITH myquery AS ('.$sql.') SELECT * FROM myquery LIMIT 0');
	for ($i = 0; $i < $rs->columnCount(); $i++) {
	    $col = $rs->getColumnMeta($i);
	    $columns[] = $col['name'];
	}
	
	$fp = fopen('temp/anagrafica.csv', 'w');	
	fputcsv($fp, $columns, ';');
	foreach ($results as $fields) {
	    fputcsv($fp, $fields, ';');
	}
	fclose($fp);
	echo('<a href="temp/anagrafica.csv">Scarica l\'anagrafica in CSV</a>');
} else if($action == 'agentiareeprodotti'){
	$sql = 'SELECT nomeagente, cognome, prodotto, aree.nome || \' \' || substring(aree.codice,4,2) as microarea FROM (SELECT agenti.nome as nomeagente, agenti.cognome, prodotti.nome as prodotto, aree.codice FROM "agente-prodotto-area" AS apa, "agente-prodotto" AS ap, "agente-aree" AS aa, aree, agenti, prodotti WHERE apa.idagenteprodotto = ap.id AND apa.idagentearea = aa.id AND ap.idagente = agenti.id AND aa.area = aree.codice AND ap.codprodotto = prodotti.id) AS myquery FULL JOIN aree ON myquery.codice = aree.codice';
	$query = $db->prepare($sql);
	$query->execute();
	$results = $query->fetchAll(PDO::FETCH_NUM);
	$columns = array();
	$rs = $db->query('WITH myquery AS ('.$sql.') SELECT * FROM myquery LIMIT 0');
	for ($i = 0; $i < $rs->columnCount(); $i++) {
	    $col = $rs->getColumnMeta($i);
	    $columns[] = $col['name'];
	}
	
	$fp = fopen('temp/agentiareeprodotti.csv', 'w');	
	fputcsv($fp, $columns, ';');
	foreach ($results as $fields) {
	    fputcsv($fp, $fields, ';');
	}
	fclose($fp);
	echo('<a href="temp/agentiareeprodotti.csv">Scarica l\'associazione agenti-aree-prodotti in CSV</a>');
	
}
else{
	echo('<br><a href="index.php?section=agentlist&action=piva">Mostra solo agenti con P.IVA</a> &nbsp &nbsp  &nbsp  &nbsp ');
	echo('<a href="index.php?section=agentlist&action=nopiva">Mostra solo agenti senza P.IVA</a> &nbsp &nbsp  &nbsp  &nbsp');
	echo('<a href="index.php?section=agentlist&action=inactive">Mostra agenti inattivi</a> &nbsp &nbsp  &nbsp  &nbsp');
	echo('<a href="index.php?section=agentlist&action=agentiareeprodotti">Esporta l\'associazione agenti-aree-prodotti</a> &nbsp &nbsp  &nbsp  &nbsp');
	echo('<a href="index.php?section=agentlist&action=esportaanagrafica">Esporta anagrafica</a><br><br>');
	echo('<strong><a href="index.php?section=addagent&action=add">Aggiungi nuovo agente</a></strong><br>');
	echo('<div  class="CSS_Table_Example" style="width:70%;" > ');
	echo('              <table >
		            <tr>
		                <td>
		                    Cognome
		                </td>
		                <td >
		                    Nome
		                </td>
		                <td>
		                    Codice Fiscale
		                </td>
				<td>
		                    P.IVA
		                </td>
				<td>
		                    e-mail
		                </td>
				<td>
		                    Dettagli
		                </td>
				<td>
					Modifica
				</td>
		            </tr>');

		$q = '';
		$attivo = 'WHERE attivo = true';
		if($action == 'piva')
		{

			$q = "WHERE partitaiva <> ''";
			$attivo = 'AND attivo = true';

		}
		else if($action == 'nopiva')
		{
			$q = "WHERE partitaiva IS NULL OR partitaiva = ''";
			$attivo = 'AND attivo = true';
		}

		else if($action == 'inactive')
		{
			$attivo = 'WHERE attivo = false';

		}


	try{
	$query = $db->prepare('SELECT * FROM agenti '.$q.' ' .$attivo.'  ORDER BY cognome');
	$query->execute();
	$results = $query->fetchAll(PDO::FETCH_ASSOC);
	}catch(Exception $pdoe){
				echo('Errore: '.$pdoe->getMessage());
			}


	foreach ($results as $row){
	 echo('            <tr>
		                <td >
		                    '.$row['cognome'].'
		                </td>
		                <td>
		                    '.$row['nome'].'
		                </td>
		                <td>
		                    '.$row['codicefiscale'].'
		                </td>
				<td>
		                    '.$row['partitaiva'].'
		                </td>
				<td>
		                    <a href="mailto:'.$row['email'].'">'.$row['email'].'</a>
		                </td>
				<td>
		                    <a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a>
		                </td>
				<td>
		                    <a href="index.php?section=addagent&action=mod&id='.$row['id'].'">modifica</a>
		                </td>
		            </tr>');
		           
	}
	echo('              </table>
		    </div>
		');
}

//echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
//foreach ($results as $row){
//	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
//}
//echo('</table>');	

?>
