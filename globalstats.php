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
	$microarea = $_POST['microarea'];
	$provincia = $_POST['provincia'];
	$regione = $_POST['regione'];
	$progressivo = $_POST['progressivo']?true:false;


	/*$tipoquery = 'ims';
	//$idagente = 2;
	$idprodotto = 4;
	$annomesestart = '201512';
	$annomeseend = '201604';
	$groupbyarea=true;
	$regione='PIEMONTE';
	$progressivo = false;*/

	if($tipoquery == 'ims'){
		$select.= 'sum(numeropezzi) AS numeropezzi, sum(numeropezzi* prezzonetto) AS nettofatturato, sum(numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome';
		$from = 'storico, prodotti';
		$where.= 'idprodotto = prodotti.id AND ';
	} 
	else if($tipoquery == 'farmacie'){
		$select.= 'sum(numeropezzi) AS numeropezzi, sum(numeropezzi* prezzonetto) AS nettofatturato, sum(numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome';
		$from = '"compensi-farmacie", prodotti';
		$where.= 'idprodotto = prodotti.id AND ';
	}
	else if($tipoquery == 'farmacieims'){
		$select.= 'sum(numeropezzi) AS numeropezzi, sum(nettofatturato) AS nettofatturato, sum(provvigioni) AS provvigioni, nome';
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

	if($tipoquery == 'ims'){
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
			$select.= ", codarea";
			$groupby.="codarea, ";
		}else if($raggruppa=='provincia'){
			$from.=', aree';
			$select.= ", aree.nome AS provincia";
			$where.= "aree.codice = codarea AND ";
			$groupby.="provincia, ";
		}
	}
	

	$groupby.="idprodotto, nome";
	$orderby.="nome";

	$sql = "SELECT $select FROM $from WHERE $where TRUE GROUP BY $groupby ORDER BY $orderby";
	echo $sql;
}

if($action=='form'){
	$queryprodotti = $db->prepare('SELECT id, nome FROM prodotti ORDER BY nome');
	$queryagenti = $db->prepare('SELECT id, nome, cognome FROM agenti ORDER BY cognome');
	$queryannomese = $db->prepare('SELECT DISTINCT annomese FROM storico ORDER BY annomese');

	echo('<form method="POST" action="index.php?section=globalstats&action=results">');
	echo('<table>');
	echo('<tr><td>Tipo query: </td><td><select name="tipoquery"><option value="ims">SOLO IMS</option><option value="farmacie">SOLO FARMACIE</option><option value="farmacieims">IMS + FARMACIE</option></select></td></tr>');
	echo('<tr><td>Prodotto: </td><td><select name="idprodotto">');
	echo('<option value="-1">-</option>');
	$queryprodotti->execute();
	$prodotti = $queryprodotti->fetchAll(PDO::FETCH_ASSOC);
	foreach($prodotti as $prodotto){
		echo('<option value="'.$prodotto['id'].'">'.$prodotto['nome'].'</option>');
	}
	echo('</select></td></tr>');
	echo('<tr><td>Agente: </td><td><select name="idagente">');
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
	echo('<td>Progressivo: <input type="checkbox" name="progressivo" value="true"></td></tr>');
	echo('<tr><td>Regione: </td><td><select name="regione" onchange="fetch_provincie(this.value);">');
	echo('<option value="-1">-</option>');
	foreach(array_keys($provincieregioni) as $regione){
		echo('<option value="'.$regione.'">'.$regione.'</option>');
	}
	echo('</select></td>');
	echo('<td>Provincia: </td><td><select name="provincia" id="provincia" onchange="fetch_aree(this.value);"></select></td>');
	echo('<td>Microarea: </td><td><select name="microarea" id="microarea"></select></td></tr>');
	echo('<tr><td><input type="radio" name="raggruppa" value="none" checked>Non raggruppare</td>
	<td><input type="radio" name="raggruppa" value="provincia">Raggruppa per provincia</td>
  	<td><input type="radio" name="raggruppa" value="area">Raggruppa per microarea</td></tr>');
	echo('<tr><td><input type="submit" name="invia"></td></tr>');
	echo('</table></form>');
}
?>
