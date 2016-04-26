<?php
include('db.php');
include_once('util.php');
$action = $_GET['action'];

if($action != 'anno'){

try{
$query = $db->prepare('SELECT DISTINCT substring(annomese from 0 for 5) as anno FROM storico');
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
		echo('<option value="anno">'.$anno['anno'].'</option>');
	}
	echo('</select><input type="submit" value="Invia"></form>');	
	echo('</div></div>');

}
if($action == 'anno'){

	try{
	$anno = $_POST['anno'];

	$query = $db->prepare('SELECT minimale FROM enasarco');
			$query->execute();
			$arrayminimale = $query->fetch();

	$query = $db->prepare('SELECT massimale FROM enasarco');
			$query->execute();
			$arraymassimale = $query->fetch();



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



echo('<div  class="CSS_Table_Example" style="width:80%;" > ');
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
$query = $db->prepare('SELECT * FROM agenti '.$q.' ' .$attivo.'  ORDER BY cognome');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}


foreach ($results as $row){

$enasarco1 = 0;
$enasarco2 = 0;
$enasarco3 = 0;
$enasarco4 = 0;
$credito = 0;
//calcolo enasarco in tutti i trimestri
$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico WHERE annomese = ANY (:primotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':primotrimestre' => php_to_postgres_array($primotrimestre)));
		$sumstorico = $query->fetch();


		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie" WHERE annomese = ANY (:primotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':primotrimestre' => php_to_postgres_array($primotrimestre)));
		$sumcompensifarmacie = $query->fetch();

		$sumimponibileprimo = $sumstorico + $sumcompensifarmacie;
		$tempcalcenasarco = - round(($sumimponibileprimo*$this->enasarco/100),2);

		if( $tempcalcenasarco < $arrayminimale[0]){
			$enasarco1 = $arrayminimale[0];	
			$credito = $arrayminimale[0] - $enasarco1;		
		}
		else if($tempcalcenasarco >= $arrayminimale[0])
		{
			$enasarco1 = $tempcalcenasarco;
		}

		if($tempcalcenasarco >= $arraymassimale[0])
		{
			$enasarco1 = $arraymassimale[0];
		}






//calcolo enasarco secondo trimestre
$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico WHERE annomese = ANY (:secondotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':secondotrimestre' => php_to_postgres_array($secondotrimestre)));
		$sumstorico = $query->fetch();


		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie" WHERE annomese = ANY (:secondotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':secondotrimestre' => php_to_postgres_array($secondotrimestre)));
		$sumcompensifarmacie = $query->fetch();

		$sumimponibilesecondo = $sumstorico + $sumcompensifarmacie;
		$tempcalcenasarco = - round(($sumimponibilesecondo*$this->enasarco/100),2);


		if( $tempcalcenasarco < $arrayminimale[0]){
			$enasarco2 = $arrayminimale[0];	
			$credito += $arrayminimale[0] - $enasarco2;	
		}
		else if($tempcalcenasarco >= $arrayminimale[0])
		{
			$enasarco2 = $tempcalcenasarco;
			if($enasarco2 - $credito >= $arrayminimale[0])
			{
				$enasarco2 -= $credito;
				$credito = 0;

			} 
			else
			{
				$ensarco2 = $arrayminimale[0];
				$credito -= $enasarco2 - $arrayminimale[0];
				
			}

		}

		if( $enasarco1 > $arraymassimale[0])
		{
			$enasarco2 =0;
		}
		else if(($enasarco1 + $enasarco2) >= $arraymassimale[0])
		{

			$enasarco2 = ($enasarco1 + $enasarco2) - $arraymassimale[0];
		}


//calcolo enasarco terzo trimestre

$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico WHERE annomese = ANY (:terzotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':terzotrimestre' => php_to_postgres_array($terzotrimestre)));
		$sumstorico = $query->fetch();


		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie" WHERE annomese = ANY (:terzotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':terzotrimestre' => php_to_postgres_array($terzotrimestre)));
		$sumcompensifarmacie = $query->fetch();

		$sumimponibileterzo = $sumstorico + $sumcompensifarmacie;
		$tempcalcenasarco = - round(($sumimponibileterzo*$this->enasarco/100),2);


		if( $tempcalcenasarco < $arrayminimale[0]){
			$enasarco3 = $arrayminimale[0];	
			$credito += $arrayminimale[0] - $enasarco3;	
		}
		else if($tempcalcenasarco >= $arrayminimale[0])
		{
			$enasarco3 = $tempcalcenasarco;
			if($enasarco3 - $credito >= $arrayminimale[0])
			{
				$enasarco3 -= $credito;
				$credito = 0;

			} 
			else
			{
				$ensarco3 = $arrayminimale[0];
				$credito -= $enasarco2 - $arrayminimale[0];
				
			}

		}

		if( $enasarco1 + $enasarco2 > $arraymassimale[0])
		{
			$enasarco3 =0;
		}
		else if(($enasarco1 + $enasarco2 + $enasarco3) >= $arraymassimale[0])
		{

			$enasarco2 = ($enasarco1 + $enasarco2 + $enasarco3) - $arraymassimale[0];
		}


//calcolo enasarco per quarto trimestre

$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico WHERE annomese = ANY (:quartotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':quartotrimestre' => php_to_postgres_array($quartotrimestre)));
		$sumstorico = $query->fetch();


		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie" WHERE annomese = ANY (:quartotrimestre ::varchar[]) and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':quartotrimestre' => php_to_postgres_array($quartotrimestre)));
		$sumcompensifarmacie = $query->fetch();

		$sumimponibilequarto = $sumstorico + $sumcompensifarmacie;
		$tempcalcenasarco = - round(($sumimponibilequarto*$this->enasarco/100),2);


		if( $tempcalcenasarco < $arrayminimale[0]){
			$enasarco4 = $arrayminimale[0];	
			$credito += $arrayminimale[0] - $enasarco4;	
		}
		else if($tempcalcenasarco >= $arrayminimale[0])
		{
			$enasarco4 = $tempcalcenasarco;
			if($enasarco4 - $credito >= $arrayminimale[0])
			{
				$enasarco4 -= $credito;
				$credito = 0;

			} 
			else
			{
				$ensarco4 = $arrayminimale[0];
				$credito -= $enasarco4 - $arrayminimale[0];
				
			}

		}

		if( $enasarco1 + $enasarco2 + $enasarco3 > $arraymassimale[0])
		{
			$enasarco4 =0;
		}
		else if(($enasarco1 + $enasarco2 + $enasarco3 + $enasarco4) >= $arraymassimale[0])
		{

			$enasarco4 = ($enasarco1 + $enasarco2 + $enasarco3 + $enasarco4) - $arraymassimale[0];
		}

//calcolo firr

$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from storico WHERE annomese = ANY :annointero and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':annointero' => php_to_postgres_array($annointero)));
		$sumstorico = $query->fetch();


		$query = $db->prepare('SELECT SUM(prezzonetto*numeropezzi*(provvigione/100)) from "compensi-farmacie" WHERE annomese = ANY :annointero and idagente = :idagente  GROUP BY idagente');
		$query->execute(array(':idagente' => $row['id'], ':annointero' => php_to_postgres_array($annointero)));
		$sumcompensifarmacie = $query->fetch();

		$sumimponibileannointero = $sumstorico + $sumcompensifarmacie;

		if($sumimponibileannointero < 6200)
			$firr = $sumimponibileannointero*0.04;
		else if($sumimponibileannointero < 9300)
			$firr = 6200*0.04 + (($sumimponibileannointero-6200)*0.02);
		else
			$firr = 6200*0.04 + (9300*0.02) + ($sumimponibileannointero-9300)*0.01;
		

//calcolo indennità

 echo('            <tr>
                        <td >
                            '.$row['cognome'].'
                        </td>
                        <td>
                            '.$row['nome'].'
                        </td>
                        <td>
                            '.$row['codicefiscale'].'ca
                        </td>
			<td>
                            '.$enasarco1.'
                        </td>
			<td>
                           '.$enasarco2.'
                        </td>
			<td>
                            '.$enasarco3.'
                        </td>
			<td>
                           '.$enasarco4.'
                        </td>
			<td>
                            '.$firr.'
                        </td>
			<td>
                           '.$indennita.'
                        </td>
                    </tr>');
                   
}
echo('              </table>
            </div>
	');
}catch(Exception $e)
{

echo($e->getMessage());
}
}
//echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
//foreach ($results as $row){
//	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
//}
//echo('</table>');	

?>
