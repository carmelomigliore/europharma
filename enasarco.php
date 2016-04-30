<?php
include('db.php');
include_once('util.php');
$action = $_GET['action'];
define('SCAGLIONE_ANNI1', '3');
define('SCAGLIONE_ANNI2', '6');
define('PERC1', '0.03');
define('PERC2', '0.035');
define('PERC3', '0.04');
define('MAXINDENNITA', 45000);


$query = $db->prepare('SELECT minimale FROM enasarco');
			$query->execute();
			$arrayminimale = $query->fetch();

	$query = $db->prepare('SELECT massimale FROM enasarco');
			$query->execute();
			$arraymassimale = $query->fetch();


if($action == "modmaxmin")
{

	$massimale = $_POST['massimale'];
	$minimale = $_POST['minimale'];
	$query = $db->prepare('UPDATE enasarco SET massimale = :massimale, minimale = :minimale WHERE id = 1');
			$query->execute(array(':massimale' => $massimale, ':minimale' => $minimale));

	echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=enasarco&action=form">Torna indietro</a>');

}


else if($action == 'form'){

try{
$query = $db->prepare('SELECT DISTINCT substring(annomese from 0 for 5) as anno FROM storico ORDER BY anno DESC');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);



}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}




echo('<div class="caricodati" align="center" style="width:300px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1><p>Seleziona Anno di interesse</p></h1>
		</div>');
	echo('<form method="POST" action="index.php?section=enasarco&action=anno">');
	echo('<select name="anno">');
	foreach($results as $anno){
		echo('<option value="'.$anno['anno'].'">'.$anno['anno'].'</option>');
	}
	echo('</select><input type="submit" value="Invia"></form>');	
	echo('</div></div>');

echo('<br><br><form method="POST" action="index.php?section=enasarco&action=modmaxmin">');
echo('<div class="caricodati" align="center" style="width:600px;"><div id="portfolio" class="container"><div class="title"><h1><p>Modifica Valori Enasarco</p></h1><table >
				<tr><td>');
	echo('Massimale: </td><td><input type="text" name="massimale" value="'.$arraymassimale[0].'" required="required"><br>');
	echo('</td></tr><tr><td>Minimale: </td><td><input type="text" name="minimale" value="'.$arrayminimale[0].'" required="required"><br>');
	echo('</td></tr></table>');
	echo('<input type="submit" name="Invia" value="Modifica">');
	echo('</form>');

}
else if($action == 'anno'){

	try{
	$anno = $_POST['anno'];


	$start = $anno;
	$start .="01";

	$end = $anno;
	$end .= "03";

	$primotrimestre = getMesiIntervallo($start, $end);

	$start = $anno;
	$start .="04";

	$end = $anno;
	$end .= "06";

	$secondotrimestre = getMesiIntervallo($start, $end);

	$start = $anno;
	$start .="06";

	$end = $anno;
	$end .= "09";

	$terzotrimestre = getMesiIntervallo($start, $end);

	$start = $anno;
	$start .="10";

	$end = $anno;
	$end .= "12";

	$quartotrimestre = getMesiIntervallo($start, $end);


	$start = $anno;
	$start .="01";

	$end = $anno;
	$end .= "12";

	$annointero = getMesiIntervallo($start, $end);



echo('<br><br><div  class="CSS_Table_Example" style="width:90%;" > ');
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
                            Primo Trimestre
                        </td>
			<td>
                            Secondo Trimestre
                        </td>
			<td>
                            Terzo Trimestre
                        </td>
			<td>
				Quarto Trimestre
			</td>
			<td>
				FIRR
			</td>
			<td>
				Indennità di Clientela
			</td>
                    </tr>');


try{
$query = $db->prepare('SELECT * FROM agenti WHERE attivo = true AND EXTRACT(YEAR FROM datainiziocontratto) <= :anno AND tipoattivita <> \'CapoArea\' AND enasarco > 0 ORDER BY cognome');
$query->execute(array(':anno' => $anno));
$results = $query->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}


$querystorico = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico, agenti WHERE storico.idagente = agenti.id AND annomese = ANY (:intervallo ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
$querycapiarea = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "storico-capiarea", agenti WHERE "storico-capiarea".idagente = agenti.id AND annomese = ANY (:intervallo ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
$queryftlibere = $db->prepare('SELECT SUM(imponibile) from storicoftlibere, agenti WHERE storicoftlibere.idagente = agenti.id AND annomese = ANY (:intervallo ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');
$queryfarmacie = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie", agenti WHERE "compensi-farmacie".idagente = agenti.id AND liquidato = ANY ( :intervallo  ::varchar[]) and (idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');

$querycapiareafarmacie = $db->prepare('SELECT SUM(scf.prezzonetto*farmacie.numeropezzi*(scf.percentuale/100)) from "storico-capiarea-farmacie" as scf, agenti, farmacie WHERE scf.idagente = agenti.id AND scf.annomesefattura = farmacie.annomese AND scf.numerofattura = farmacie.numerofattura AND scf.idprodotto = farmacie.idprodotto AND scf.annomese = ANY (:intervallo ::varchar[]) and (scf.idagente = :idagente OR codicefiscale = :codicefiscale)  GROUP BY agenti.cognome');

foreach ($results as $row){

		$codicefiscaledacercare = $row['codicefiscale'].'-';

		$enasarco1 = 0;
		$enasarco2 = 0;
		$enasarco3 = 0;
		$enasarco4 = 0;
		$credito = 0;
//calcolo enasarco in tutti i trimestri
		
		$querystorico->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($primotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $querystorico->fetch();	
		
		$querycapiarea->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($primotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $querycapiarea->fetch();

		$queryftlibere->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($primotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $queryftlibere->fetch();
		
		$queryfarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($primotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $queryfarmacie->fetch();
		
		$querycapiareafarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($primotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensicapiareafarmacie = $querycapiareafarmacie->fetch();

		//echo('bubba'.$sumstoricocapiarea[0]);
		
		$sumimponibileprimo = $sumstorico[0] + $sumcompensifarmacie[0]+$sumftlibere[0] + $sumstoricocapiarea[0] + $sumcompensicapiareafarmacie[0];
		$tempcalcenasarco = round(($sumimponibileprimo*$row['enasarco']/100),2);
		
		if( $tempcalcenasarco < $arrayminimale[0]){
			$credito = $arrayminimale[0] - $tempcalcenasarco;
			$enasarco1 = $arrayminimale[0];	
			
				
		}
		else if($tempcalcenasarco >= $arrayminimale[0])
		{
			$enasarco1 = $tempcalcenasarco;
		}

		if($tempcalcenasarco >= $arraymassimale[0])
		{
			$enasarco1 = $arraymassimale[0];
		}
		if($sumimponibileprimo == 0)
			$enasarco1=0;






//calcolo enasarco secondo trimestre
$minimale2 = $arrayminimale[0] * 2;

		$querystorico->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($secondotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $querystorico->fetch();	
		
		$querycapiarea->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($secondotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $querycapiarea->fetch();

		$queryftlibere->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($secondotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $queryftlibere->fetch();
		
		$queryfarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($secondotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $queryfarmacie->fetch();
		
		$querycapiareafarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($secondotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensicapiareafarmacie = $querycapiareafarmacie->fetch();

		
		
		$sumimponibilesecondo = $sumstorico[0] + $sumcompensifarmacie[0]+$sumftlibere[0] + $sumstoricocapiarea[0] + $sumcompensicapiareafarmacie[0];
		$tempcalcenasarco = round(($sumimponibilesecondo*$row['enasarco']/100),2);


		if( $tempcalcenasarco + $enasarco1 < $minimale2){
			$credito += $minimale2 - $tempcalcenasarco - $enasarco1;
			$enasarco2 = $minimale2 - $enasarco1;	
			
			
		}
		else if($tempcalcenasarco + $enasarco1 >= $minimale2)
		{
			$enasarco2 = $tempcalcenasarco;
			if($enasarco2 + $enasarco1 - $credito >= $minimale2)
			{
				$enasarco2 -= $credito;
				$credito = 0;
				

			} 
			else
			{
				$ensarco2 = $minimale2 - $enasarco1;
				$credito -= $enasarco1+$enasarco2 - $minimale2;
				
				
			}

		}

		if( $enasarco1 > $arraymassimale[0])
		{
			$enasarco2 =0;
		}
		else if(($enasarco1 + $enasarco2) >= $arraymassimale[0])
		{

			$enasarco2 =  $arraymassimale[0] - $enasarco1;
		}
		if($sumimponibilesecondo + $sumimponibileprimo == 0)
			$enasarco2=0;


//calcolo enasarco terzo trimestre
$minimale3 = $arrayminimale[0] * 3;

		$querystorico->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($terzotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $querystorico->fetch();	
		
		$querycapiarea->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($terzotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $querycapiarea->fetch();

		$queryftlibere->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($terzotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $queryftlibere->fetch();
		
		$queryfarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($terzotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $queryfarmacie->fetch();
		
		$querycapiareafarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($terzotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensicapiareafarmacie = $querycapiareafarmacie->fetch();

		
		
		$sumimponibileterzo = $sumstorico[0] + $sumcompensifarmacie[0]+$sumftlibere[0] + $sumstoricocapiarea[0] + $sumcompensicapiareafarmacie[0];
		
		$tempcalcenasarco = round(($sumimponibileterzo*$row['enasarco']/100),2);


		if( $tempcalcenasarco + $enasarco1 + $enasarco2 < $minimale3){
			$credito += $minimale3 - $tempcalcenasarco - $enasarco1 - $enasarco2;	
			$enasarco3 = $minimale3 - $enasarco1 - $enasarco2;	
			
		}
		else if($tempcalcenasarco + $enasarco1 + $enasarco2 >= $minimale3)
		{
			$enasarco3 = $tempcalcenasarco;
			if($enasarco3 + $enasarco2 + $enasarco1 - $credito >= $minimale3)
			{
				$enasarco3 -= $credito;
				$credito = 0;

			} 
			else
			{
				$ensarco3 = $minimale - $enasarco1 - $enasarco2;
				$credito -= $enasarco1 + $enasarco2 + $enasarco3 - $minimale3;
				
			}

		}

		if( $enasarco1 + $enasarco2 > $arraymassimale[0])
		{
			$enasarco3 =0;
		}
		else if(($enasarco1 + $enasarco2 + $enasarco3) >= $arraymassimale[0])
		{

			$enasarco3 = $arraymassimale[0]- $enasarco1 - $enasarco2;
		}
		
		if($sumimponibileterzo + $sumimponibilesecondo + $sumimponibileprimo == 0)
			$enasarco3=0;



//calcolo enasarco per quarto trimestre
$minimale4 = $arrayminimale[0] * 4;
		$querystorico->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($quartotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $querystorico->fetch();	
		
		$querycapiarea->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($quartotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $querycapiarea->fetch();

		$queryftlibere->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($quartotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $queryftlibere->fetch();
		
		$queryfarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($quartotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $queryfarmacie->fetch();
		
		$querycapiareafarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($quartotrimestre).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensicapiareafarmacie = $querycapiareafarmacie->fetch();

		//echo('bubba'.$sumstoricocapiarea[0]);
		
		$sumimponibilequarto = $sumstorico[0] + $sumcompensifarmacie[0]+$sumftlibere[0] + $sumstoricocapiarea[0] + $sumcompensicapiareafarmacie[0];
		
		$tempcalcenasarco = round(($sumimponibilequarto*$row['enasarco']/100),2);


		if( $tempcalcenasarco + $enasarco1 + $enasarco2 + $enasarco3 < $minimale4){
			$credito += $minimale4 - $tempcalcenasarco - $enasarco1 - $enasarco2 - $enasarco3;	
			$enasarco4 = $minimale4 - $enasarco1 - $enasarco2 - $enasarco3;	
			
		}
		else if($tempcalcenasarco + $enasarco1 + $enasarco2 + $enasarco3 >= $minimale4)
		{
			$enasarco4 = $tempcalcenasarco;
			if($enasarco4 + $enasarco3 + $enasarco2 +$enasarco1 - $credito >= $minimale4)
			{
				$enasarco4 -= $credito;
				$credito = 0;

			} 
			else
			{
				$ensarco4 = $minimale4 - $enasarco1 - $enasarco2 - $enasarco3;
				$credito -= $enasarco4 + $enasarco3 + $enasarco2 + $enasarco1 - $minimale4;
				
			}

		}

		if( $enasarco1 + $enasarco2 + $enasarco3 > $arraymassimale[0])
		{
			$enasarco4 =0;
		}
		else if(($enasarco1 + $enasarco2 + $enasarco3 + $enasarco4) >= $arraymassimale[0])
		{

			$enasarco4 = $arraymassimale[0] - $enasarco1 - $enasarco2 - $enasarco3;
		}
		if($sumimponibilequarto + $sumimponibileterzo + $sumimponibilesecondo + $sumimponibileprimo == 0)
			$enasarco4=0;

//calcolo firr

		$querystorico->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($annointero).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstorico = $querystorico->fetch();	
		
		$querycapiarea->execute(array(':idagente' => $row['id'], ':intervallo' => '{ '.php_to_postgres_array($annointero).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumstoricocapiarea = $querycapiarea->fetch();

		$queryftlibere->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($annointero).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumftlibere = $queryftlibere->fetch();
		
		$queryfarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($annointero).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensifarmacie = $queryfarmacie->fetch();
		
		$querycapiareafarmacie->execute(array(':idagente' => $row['id'], ':intervallo' => '{'.php_to_postgres_array($annointero).'}', ':codicefiscale' => $codicefiscaledacercare));
		$sumcompensicapiareafarmacie = $querycapiareafarmacie->fetch();

		//echo('<br>bubba'.$sumstoricocapiarea[0].'/'.$sumstorico[0].'/'.$sumcompensifarmacie[0].'/'.$sumcompensicapiareafarmacie[0].'/'.$sumftlibere[0]);
		
		$sumimponibileannointero = $sumstorico[0] + $sumcompensifarmacie[0]+$sumftlibere[0] + $sumstoricocapiarea[0] + $sumcompensicapiareafarmacie[0];

		if($sumimponibileannointero < 6200)
			$firr = $sumimponibileannointero*0.04;
		else if($sumimponibileannointero < 9300)
			$firr = 6200*0.04 + (($sumimponibileannointero-6200)*0.02);
		else
			$firr = 6200*0.04 + (9300*0.02) + ($sumimponibileannointero-9300)*0.01;
		

//calcolo indennità

$annoiniziocontratto = substr($row['datainiziocontratto'],0,4);

$annilavoro = $anno - $annoiniziocontratto;
$indennita = 0;

if($annilavoro <= SCAGLIONE_ANNI1)
	$indennita = $sumimponibileannointero*PERC1;
else if($annilavoro > SCAGLIONE_ANNI1 && $annilavoro <= SCAGLIONE_ANNI2)
	$indennita = $sumimponibileannointero*PERC2;
else
	$indennita = $sumimponibileannointero*PERC3;

if($indennita > MAXINDENNITA){
	$indennita = MAXINDENNITA;
}

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
                            '.number_format($enasarco1,2,',','.').'
                        </td>
			<td>
                           '.number_format($enasarco2,2,',','.').'
                        </td>
			<td>
                            '.number_format($enasarco3,2,',','.').'
                        </td>
			<td>
                           '.number_format($enasarco4,2,',','.').'
                        </td>
			<td>
                            '.number_format($firr,2,',','.').'
                        </td>
			<td>
                           '.number_format($indennita,2,',','.').'
                        </td>
                    </tr>');
                   
}
echo('              </table>
            </div>
	');
}catch(Exception $e)
{

echo($e->getMessage().' Line:'.$e->getLine());
}
}
//echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
//foreach ($results as $row){
//	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
//}
//echo('</table>');	

?>
