<?php
include('util.php');
$select = '';
$from = '';
$where = '';
$groupby = '';
$orderby = '';

$tipoquery = 'ims';
//$idagente = 2;
$idprodotto = 4;
$annomese = '201512';
$microarea='00105';
$groupbyarea=true;

if($tipoquery == 'ims'){
	$select.= 'sum(numeropezzi) AS numeropezzi, sum(numeropezzi* prezzonetto) AS nettofatturato, sum(numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome';
	$from = 'storico, prodotti';
	$where.= 'storico.idprodotto = prodotti.id AND ';
} 
else if($tipoquery == 'farmacie'){
	$select.= 'sum(numeropezzi) AS numeropezzi, sum(numeropezzi* prezzonetto) AS nettofatturato, sum(numeropezzi* prezzonetto * provvigione/100) AS provvigioni, prodotti.nome';
	$from = '"compensi-farmacie", prodotti';
	$where.= '"compensi-farmacie".idprodotto = prodotti.id AND ';
}
else if($tipoquery == 'farmacieims'){
	$select.= 'sum(storico.numeropezzi) + sum(farm.numeropezzi) AS numeropezzi, sum(storico.numeropezzi* storico.prezzonetto) + sum(farm.numeropezzi* farm.prezzonetto) AS nettofatturato, sum(storico.numeropezzi* storico.prezzonetto * storico.provvigione/100) + sum(farm.numeropezzi* farm.prezzonetto * farm.provvigione/100) AS provvigioni, prodotti.nome';
	$from = '"compensi-farmacie" AS farm, storico, prodotti';
	$where.= 'farm.idprodotto = prodotti.id AND farm.idprodotto = storico.idprodotto AND farm.annomese = storico.annomese AND ';
}

if(isset($idagente)){
	$where.="idagente = $idagente AND ";
}

if(isset($idprodotto)){
	$where.="idprodotto = $idprodotto AND ";
}

if(isset($annomese)){
	$where.="annomese = $annomese::varchar AND ";
	//$groupby.="annomese, ";
}
else if(isset($intervallomesi)){
	//$where.='annomese = ANY '.getMesiIntervallo($intervallomesi);
}

if($tipoquery != 'farmacieims'){
	if(isset($microarea)){
		$where.="codarea = '$microarea'::varchar AND ";
	}
	else if(isset($provincia)){
		//$where.='codarea = ANY '.php_to_postgres_array(getAreeProvincia($provincia)).' AND ';
		if($groupbyarea){
			$select.= ", codarea";
			$groupby.="codarea, ";
		}
	}
	else if(isset($regione)){
		//$where.='codarea = ANY '.php_to_postgres_array(getAreeRegione($regione)).' AND ';
		if($groupbyarea){
			//$groupby.="provincia, ";
			//$select.= ", provincia";
		}
	}
}

$groupby.="idprodotto, prodotti.nome";
$orderby.="prodotti.nome";

$sql = "SELECT $select FROM $from WHERE $where TRUE GROUP BY $groupby ORDER BY $orderby";
echo $sql;


?>
