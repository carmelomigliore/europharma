<script type="text/javascript" src="js/jquery.js"></script>
<script type="text/javascript">

function fetch_provincie(val)
{
   $.ajax({
     type: 'post',
     url: 'globalstats.php?action=getprovincie',
     data: {
       get_option:val
     },
     success: function (response) {
       document.getElementById("provincia").innerHTML=response; 
     }
   });
}

function fetch_aree(val)
{
   $.ajax({
     type: 'post',
     url: 'globalstats.php?action=getaree',
     data: {
       get_option:val
     },
     success: function (response) {
       document.getElementById("microarea").innerHTML=response; 
     }
   });
}

</script>
<?php
include('util.php');
include('db.php');

$action=$_GET['action'];

$provincieregioni = array("VALLE D'AOSTA" => array("AOSTA"), "PIEMONTE" => array("TORINO", "CUNEO", "ALESSANDRIA", "NOVARA", "VERCELLI", "ASTI", "BIELLA", "VERBANIA"), "LIGURIA" => array("GENOVA", "IMPERIA", "SAVONA", "LA SPEZIA"), "LOMBARDIA" => array("MILANO", "VARESE", "COMO", "PAVIA", "MANTOVA", "MONZA-BRIANZA", "BERGAMO", "BRESCIA", "LODI", "CREMONA", "LECCO", "SONDRIO"), "VENETO" => array("VERONA", "PADOVA", "VICENZA", "VENEZIA", "BELLUNO", "ROVIGO", "TREVISO"), "TRENTINO-ALTO ADIGE" => array("TRENTO", "BOLZANO"), "FRIULI-VENEZIA GIULIA" => array("PORDENONE", "UDINE", "GORIZIA", "TRIESTE"), "EMILIA-ROMAGNA" => array("PARMA", "PIACENZA", "REGGIO EMILIA", "MODENA", "BOLOGNA", "FERRARA", "FORLI - CESENA", "RIMINI", "RAVENNA"), "TOSCANA" => array("FIRENZE", "PRATO", "PISTOIA", "LUCCA", "PISA", "LIVORNO", "GROSSETO", "MASSA CARRARA", "SIENA", "AREZZO"), "UMBRIA" => array("PERUGIA", "TERNI"), "MARCHE" => array("PESARO URBINO", "ANCONA", "ASCOLI PICENO", "MACERATA", "FERMO"), "LAZIO" => array("ROMA", "LATINA", "FROSINONE", "VITERBO", "RIETI"), "ABRUZZO" => array("L'AQUILA'", "PESCARA", "CHIETI", "TERAMO"), "MOLISE" => array("CAMPOBASSO", "ISERNIA"), "CAMPANIA" => array("NAPOLI", "CASERTA", "BENEVENTO", "AVELLINO", "SALERNO"), "PUGLIA" => array("FOGGIA", "BAT", "BARI", "BRINDISI", "LECCE", "TARANTO"), "BASILICATA" => array("POTENZA", "MATERA"), "CALABRIA" => array("COSENZA", "CROTONE", "VIBO VALENTIA", "REGGIO CALABRIA", "CATANZARO"), "SICILIA" => array("PALERMO", "TRAPANI", "AGRIGENTO", "ENNA", "CALTANISSETTA", "MESSINA", "CATANIA", "SIRACUSA", "RAGUSA"), "SARDEGNA" => array("CAGLIARI", "SASSARI", "ORISTANO", "OLBIA TEMPIO", "OGLIASTRA", "NUORO", "CARBONIA IGLESIAS", "MEDIO CAMPIDANO"));

function getAreeProvincia($provincia){
	global $db;
	$arr = array();
	$query = $db->prepare('SELECT codice FROM aree WHERE nome = :provincia');
	$query->execute(array(':provincia' => $provincia));
	$results = $query->fetchAll(PDO::FETCH_NUM);
	foreach($results as $microarea){
		$arr[] = $microarea[0];
	}
	return $arr;
}

function getAreeRegione($regione){
	global $provincieregioni;
	$arr = array();
	foreach ($provincieregioni[$regione] as $provincia ){
		foreach(getAreeProvincia($provincia) as $microarea){
			//print_r($microarea);
			$arr[] = $microarea;
		}
	}
	return $arr;
}

if($action=='getprovincie'){
	$regione = $_POST['get_option'];
	echo('<option value="-1">-</option>');
	foreach($provincieregioni[$regione] as $provincia){
		echo('<option value="'.$provincia.'">'.$provincia.'</option>');
	}
}

if($action=='getaree'){
	$provincia = $_POST['get_option'];
	echo('<option value="-1">-</option>');
	foreach(getAreeProvincia($provincia) as $microarea){
		echo('<option value="'.$microarea.'">'.$provincia.' '.substr($microarea,3,2).'</option>');
	}
}

if($action=='results'){
	$select = '';
	$from = '';
	$where = '';
	$groupby = '';
	$orderby = '';

	$idagente=$_POST['idagente'];
	$tipoquery = $_POST['tipoquery'];
	$idprodotto = $_POST['idprodotto'];
	$annomesestart = $_POST['annomesestart'];
	$annomeseend = $_POST['annomeseend'];
	$annomeseconfrontostart = $_POST['annomeseconfrontostart'];
	$annomeseconfrontoend = $_POST['annomeseconfrontoend'];
	$microarea = $_POST['microarea'];
	$provincia = $_POST['provincia'];
	$regione = $_POST['regione'];
	$progressivo = $_POST['progressivo']?true:false;
	$raggruppa = $_POST['raggruppa'];
	$raggruppacollaboratori = $_POST['raggruppacollaboratori']?true:false;


	/*$tipoquery = 'ims';
	//$idagente = 2;
	$idprodotto = 4;
	$annomesestart = '201512';
	$annomeseend = '201604';
	$groupbyarea=true;
	$regione='PIEMONTE';
	$progressivo = false;*/

	if($tipoquery == 'ims'){
		$select.= 'prodotti.nome AS "Prodotto", sum(numeropezzi) AS "Numero Pezzi", replace(to_char(sum(numeropezzi* prezzonetto), \'FM999999999.00\'),\'.\',\',\') AS "Netto Fatturato", replace(to_char(sum(numeropezzi* prezzonetto * provvigione/100), \'FM999999999.00\'),\'.\',\',\') AS "Provvigioni"';
		$from = 'storico, prodotti';
		$where.= 'idprodotto = prodotti.id AND ';
	} 
	else if($tipoquery == 'imscapiarea'){
		$select.= 'prodotti.nome AS "Prodotto", sum(numeropezzi) AS "Numero Pezzi", replace(to_char(sum(numeropezzi* prezzonetto), \'FM999999999.00\'),\'.\',\',\') AS "Netto Fatturato", replace(to_char(sum(numeropezzi* prezzonetto * provvigione/100), \'FM999999999.00\'),\'.\',\',\') AS "Provvigioni"';
		$from = '"storico-capiarea" AS storico, prodotti';
		$where.= 'idprodotto = prodotti.id AND ';
	}
	else if($tipoquery == 'farmacie'){
		$select.= 'prodotti.nome AS "Prodotto", sum(numeropezzi) AS "Numero Pezzi", replace(to_char(sum(numeropezzi* prezzonetto), \'FM999999999.00\'),\'.\',\',\') AS "Netto Fatturato", replace(to_char(sum(numeropezzi* prezzonetto * provvigione/100), \'FM999999999.00\'),\'.\',\',\') AS "Provvigioni"';
		$from = '"compensi-farmacie", prodotti';
		$where.= 'idprodotto = prodotti.id AND ';
	}
	else if($tipoquery == 'farmacieims'){
		$select.= 'total.nome AS "Prodotto", sum(numeropezzi) AS "Numero Pezzi", replace(to_char(sum(nettofatturato), \'FM999999999.00\'),\'.\',\',\') AS "Netto Fatturato", replace(to_char(sum(provvigioni), \'FM999999999.00\'),\'.\',\',\') AS "Provvigioni"';
		$from = '(SELECT numeropezzi, (numeropezzi* prezzonetto) AS nettofatturato, (numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome, annomese, idagente, idprodotto 
			FROM storico, prodotti WHERE idprodotto = prodotti.id 
			UNION ALL
			SELECT numeropezzi, (numeropezzi* prezzonetto) AS nettofatturato, (numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome, annomese, idagente, idprodotto
			FROM "compensi-farmacie", prodotti WHERE idprodotto = prodotti.id) AS total';
		$where.= ' ';
	}

	if($idagente!=-1){
		$where.="idagente = $idagente AND ";
	}

	if($idprodotto!=-1){
		$where.="idprodotto = $idprodotto AND ";
	}

	

	if($tipoquery == 'ims' || $tipoquery == 'imscapiarea'){
		if(!is_null($microarea) && $microarea!=-1){
			$where.="codarea = '$microarea'::varchar AND ";
		}
		else if(!is_null($provincia) && $provincia!=-1){
			$where.='codarea = ANY (\'{'.php_to_postgres_array(getAreeProvincia($provincia)).'}\'::varchar[]) AND ';
		}
		else if($regione!=-1){
			$where.='codarea = ANY (\'{'.php_to_postgres_array(getAreeRegione($regione)).'}\'::varchar[]) AND ';
		}
		
		if($raggruppa=='area'){
			$from.=', aree';
			$where.= "aree.codice = codarea AND ";
			$select.= ", (aree.nome || ' ' || substring(codarea,4,2)) AS \"Microarea\"";
			$groupby.="codarea, aree.nome, ";
		}else if($raggruppa=='provincia'){
			$from.=', aree';
			$select.= ", aree.nome AS provincia";
			$where.= "aree.codice = codarea AND ";
			$groupby.="aree.nome, ";
		} 
	}
	
	if($raggruppacollaboratori){
		$from.=', agenti';
		$where.='agenti.id = idagente AND ';
		$select.=", (agenti.nome || ' ' || agenti.cognome) AS \"Collaboratore\"";
		$groupby.= "agenti.nome, agenti.cognome, ";
	}
	

	
	
	$where2 = $where;
	
	if($annomesestart!=-1 && $annomeseend==-1){
		
		$where.="annomese = $annomesestart::varchar AND ";
	}
	else if($annomesestart!=-1 && $annomeseend!=-1){
		$where.='annomese = ANY (\'{'.php_to_postgres_array(getMesiIntervallo($annomesestart, $annomeseend)).'}\'::varchar[]) AND ';
		if(!$progressivo){
			$select.=', annomese';
			$groupby.="annomese, ";
		}
	}
	
	if($tipoquery!='farmacieims'){
		$groupby.="idprodotto, prodotti.nome";
		$orderby.="prodotti.nome";
	}
	else{
		$groupby.="idprodotto, total.nome";
		$orderby.="total.nome";
	}

	$sql = "SELECT $select FROM $from WHERE $where TRUE GROUP BY $groupby ORDER BY $orderby";
	
	
	if($annomeseconfrontostart!=-1){
		if($annomeseconfrontostart!=-1 && $annomeseconfrontoend==-1){
			$where2.="annomese = $annomeseconfrontostart::varchar AND ";
		}
		else if($annomeseconfrontostart!=-1 && $annomeseconfrontoend != -1){
			$where2.='annomese = ANY (\'{'.php_to_postgres_array(getMesiIntervallo($annomeseconfrontostart, $annomeseconfrontoend)).'}\'::varchar[]) AND ';
		}
		$sql2 = "SELECT $select FROM $from WHERE $where2 TRUE GROUP BY $groupby ORDER BY $orderby";
		
		/*$query = $db->prepare($sql2);
		$query->execute();
		$results = $query->fetchAll(PDO::FETCH_NUM);
		$columns = array();
		$rs = $db->query('WITH myquery AS ('.$sql2.') SELECT * FROM myquery LIMIT 0');
		for ($i = 0; $i < $rs->columnCount(); $i++) {
		    $col = $rs->getColumnMeta($i);
		    $columns[] = $col['name'];
		}
	
		echo('<br><br><table border="1">');
		echo('<tr>');
		foreach ($columns as $column){
			echo('<td>'.$column.'</td>');
		}
		echo('</tr>');
	
		foreach($results as $row){
			echo('<tr>');
			foreach($row as $value){
				echo('<td>'.$value.'</td>');
			}
			echo('</tr>');
		}
		echo('</table>');*/
		$where3 = 'a."Prodotto" = b."Prodotto" AND ';
		$moreselect='';
		if(!$progressivo && $annomesestart!=-1 && $annomeseend!=-1){
			$where3.='a.annomese = b.annomese AND ';
			$moreselect.='a.annomese, ';
		}
		if($raggruppacollaboratori){
			$where3.='a."Collaboratore" = b."Collaboratore" AND ';
			$moreselect.='a."Collaboratore", ';
		}
		
		if($tipoquery == 'ims' || $tipoquery == 'imscapiarea'){
			if($raggruppa=='area'){
				$where3.='a."Microarea" = b."Microarea" AND ';
				$moreselect.='a."Microarea", ';
			}else if($raggruppa=='provincia'){
				$where3.='a.provincia = b.provincia AND ';
				$moreselect.='a.provincia, ';
			} 
		}
	
		
		
		$sqltot = 'SELECT a."Prodotto", '.$moreselect.' COALESCE(a."Numero Pezzi",0) AS "Numero Pezzi 1", COALESCE(b."Numero Pezzi",0) AS "Numero Pezzi 2", replace(to_char(((COALESCE(a."Numero Pezzi",0) - COALESCE(b."Numero Pezzi",0))/NULLIF(COALESCE(b."Numero Pezzi",0),0)*100), \'FM999999999.00\'),\'.\',\',\') AS "Diff pezzi %", COALESCE(a."Netto Fatturato",\'0\') AS "Netto Fatturato 1", COALESCE(b."Netto Fatturato",\'0\') AS "Netto Fatturato 2", replace(to_char((((replace(COALESCE(a."Netto Fatturato",\'0\'), \',\',\'.\')::real - replace(COALESCE(b."Netto Fatturato",\'0\'), \',\',\'.\')::real)/NULLIF(replace(COALESCE(b."Netto Fatturato",\'0\'), \',\',\'.\')::real,0))*100), \'FM999999999.00\'),\'.\',\',\') AS "Diff netto fatturato %", COALESCE(a."Provvigioni",\'0\') AS "Provvigioni 1", COALESCE(b."Provvigioni",\'0\') AS "Provvigioni 2", replace(to_char((((replace(COALESCE(a."Provvigioni",\'0\'), \',\',\'.\')::real - replace(COALESCE(b."Provvigioni",\'0\'), \',\',\'.\')::real)/NULLIF(replace(COALESCE(b."Provvigioni",\'0\'), \',\',\'.\')::real,0))*100), \'FM999999999.00\'),\'.\',\',\') AS "Diff provvigioni %"
		FROM ('.$sql.') AS a FULL JOIN ('.$sql2.') AS b ON '.$where3.' TRUE';
		
		//echo($sqltot);
		$sql = $sqltot;	
	}
	$query = $db->prepare($sql);
	$query->execute();
	
	$results = $query->fetchAll(PDO::FETCH_NUM);
	$columns = array();
	$rs = $db->query('WITH myquery AS ('.$sql.') SELECT * FROM myquery LIMIT 0');
	for ($i = 0; $i < $rs->columnCount(); $i++) {
	    $col = $rs->getColumnMeta($i);
	    $columns[] = $col['name'];
	}
	
	$fp = fopen('temp/statistics.csv', 'w');	
	fputcsv($fp, $columns, ';');
	foreach ($results as $fields) {
	    fputcsv($fp, $fields, ';');
	}
	fclose($fp);

	echo('<table border="1">');
	echo('<tr>');
	foreach ($columns as $column){
		echo('<td>'.$column.'</td>');
	}
	echo('</tr>');

	foreach($results as $row){
		echo('<tr>');
		foreach($row as $value){
			echo('<td>'.$value.'</td>');
		}
		echo('</tr>');
	}
	echo('</table>');
	echo('<a href="temp/statistics.csv" download>Scarica questa tabella in CSV</a>');
	
}

if($action=='form'){
	$queryprodotti = $db->prepare('SELECT id, nome FROM prodotti ORDER BY nome');
	$queryagenti = $db->prepare('SELECT id, nome, cognome FROM agenti ORDER BY cognome');
	$queryannomese = $db->prepare('SELECT DISTINCT annomese FROM storico ORDER BY annomese');

	echo('<form method="POST" action="index.php?section=globalstats&action=results">');
	echo('<table>');
	echo('<tr><td>Tipo query: </td><td><select name="tipoquery"><option value="ims">SOLO IMS</option><option value="farmacie">SOLO FARMACIE</option><option value="farmacieims">IMS + FARMACIE</option><option value="imscapiarea">IMS CAPIAREA</option></select></td></tr>');
	echo('<tr><td>Prodotto: </td><td><select name="idprodotto">');
	echo('<option value="-1">-</option>');
	$queryprodotti->execute();
	$prodotti = $queryprodotti->fetchAll(PDO::FETCH_ASSOC);
	foreach($prodotti as $prodotto){
		echo('<option value="'.$prodotto['id'].'">'.$prodotto['nome'].'</option>');
	}
	echo('</select></td></tr>');
	echo('<tr><td>Collaboratore: </td><td><select name="idagente">');
	echo('<option value="-1">-</option>');
	$queryagenti->execute();
	$agenti = $queryagenti->fetchAll(PDO::FETCH_ASSOC);
	foreach($agenti as $agente){
		echo('<option value="'.$agente['id'].'">'.$agente['cognome'].' '.$agente['nome'].'</option>');
	}
	echo('</select></td></tr>');
	echo('<tr><td>Anno e mese (start): </td><td><select name="annomesestart">');
	echo('<option value="-1">-</option>');
	$queryannomese->execute();
	$annimese = $queryannomese->fetchAll(PDO::FETCH_ASSOC);
	foreach($annimese as $annomese){
		echo('<option value="'.$annomese['annomese'].'">'.$annomese['annomese'].'</option>');
	}
	echo('</select></td>');
	echo('<td>Anno e mese (end): </td><td><select name="annomeseend">');
	echo('<option value="-1">-</option>');
	foreach($annimese as $annomese){
		echo('<option value="'.$annomese['annomese'].'">'.$annomese['annomese'].'</option>');
	}
	echo('</select></td>');
	echo('<tr><td>Anno e mese confronto(start): </td><td><select name="annomeseconfrontostart">');
	echo('<option value="-1">-</option>');
	foreach($annimese as $annomese){
		echo('<option value="'.$annomese['annomese'].'">'.$annomese['annomese'].'</option>');
	}
	echo('</select></td>');
	echo('<td>Anno e mese confronto (end): </td><td><select name="annomeseconfrontoend">');
	echo('<option value="-1">-</option>');
	foreach($annimese as $annomese){
		echo('<option value="'.$annomese['annomese'].'">'.$annomese['annomese'].'</option>');
	}
	echo('</select></td>');
	echo('<td>Progressivo: <input type="checkbox" name="progressivo" value="true"></td></tr>');
	echo('<tr><td>Regione: </td><td><select name="regione" onchange="fetch_provincie(this.value);">');
	echo('<option value="-1">-</option>');
	foreach(array_keys($provincieregioni) as $regione){
		echo('<option value="'.$regione.'">'.$regione.'</option>');
	}
	echo('</select></td>');
	echo('<td>Provincia: </td><td><select name="provincia" id="provincia" onchange="fetch_aree(this.value);"></select></td>');
	echo('<td>Microarea: </td><td><select name="microarea" id="microarea"></select></td></tr>');
	echo('<tr><td>Attenzione: i filtri geografici non hanno effetto sulle query FARMACIA</td></tr>');
	echo('<tr><td><input type="radio" name="raggruppa" value="none" checked>Non raggruppare per aree</td>
	<td><input type="radio" name="raggruppa" value="provincia">Raggruppa per provincia</td>
  	<td><input type="radio" name="raggruppa" value="area">Raggruppa per microarea</td>
  	<td><input type="checkbox" name="raggruppacollaboratori" value="true">Raggruppa per collaboratori</td></tr>');
	echo('<tr><td><input type="submit" name="invia"></td></tr>');
	echo('</table></form>');
}
?>
