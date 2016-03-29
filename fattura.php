<?php
include_once('db.php');
include_once('agent.php');
require('fpdf/invoice.php');
define('EURO',chr(128));
$id=$_GET['id'];
$action = $_GET['action'];
/*$query=$db->prepare('SELECT * FROM agenti WHERE id = :id');
$query->execute(array(':id' => $id));
$row = $query->fetch(PDO::FETCH_ASSOC);*/
$agente = Agent::getAgentFromDB($id,$db);

if($action == 'spinner')
{


	$query=$db->prepare('SELECT DISTINCT annomese FROM ims ORDER BY annomese DESC'); // seleziona i prodotti
	$query->execute();
	$annomese = $query->fetchAll(PDO::FETCH_ASSOC);
	echo('<form method="POST" action="index.php?section=fattura&action=generafattura&id='.$id.'"><select name="selection">');
	foreach($annomese as $am){
	echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
	}

	echo('</select><input type="submit" value="Genera Fattura" name="submit"/></form>');


}

if($action == 'generafattura')
{
	$annomese = $_POST['selection'];
	//$agente = Agent::getAgentFromDB($id,$db);

	$calciva = 0;
	$calcenasarco = 0;
	$calcritacconto = 0;
	$calccontributoinps = 0;
	$calcrivalsainps = 0;
	$totaledovuto = 0;
	$imponibile = 0;

	$agente->calculateSalary($db, $annomese, $calciva, $calcenasarco, $calcritacconto, $calccontributoinps, $calcrivalsainps, $totaledovuto, 	$imponibile);
	echo('<table width="70%" align="center"><tr>');
	echo('<td><p>Annomese: '.$annomese.'</p></td>');
	echo('<td><p>Calciva: '.$calciva.'</p></td>');
	echo('</tr><tr>');
	echo('<td><p>Calcenasarco: '.$calcenasarco.'</p></td>');
	echo('<td><p>Contributo inps: '.$calccontributoinps.'</p></td>');
	echo('<td><p>Rit Acconto: '.$calcritacconto.'</p></td>');
	echo('</tr><tr>');
	echo('<td><p>Rivalsa inps: '.$calcrivalsainps.'</p></td>');
	echo('<td><p>Ritenuta d\'acconto: '.$calcritacconto.'</p></td>');
	echo('</tr><tr>');
	echo('<td><p>Imponibile: '.$imponibile.'</p></td>');
	echo('<td><p>Totale dovuto: '.$totaledovuto.'</p></td>');
	echo('</tr>');
	echo('</table>'); 

	echo('<form method="POST" action="fattura.php?&action=pdf&id='.$id.'">');
	//echo('<a href="fattura.php?&action=pdf&id='.$id.'">Genera Pdf Fattura</a>'.'<br>');
	echo('<input type="hidden" value='.$annomese.' name="annomese"/>');
	echo('<input type="hidden" value='.number_format((float)$calciva,2,',','.').' name="calciva"/>');
	echo('<input type="hidden" value='.number_format((float)$calcenasarco,2,',','.').' name="calcenasarco"/>');
	echo('<input type="hidden" value='.number_format((float)$calccontributoinps,2,',','.').' name="calccontributoinps"/>');
	echo('<input type="hidden" value='.number_format((float)$calcritacconto,2,',','.').' name="calcritacconto"/>');
	echo('<input type="hidden" value='.number_format((float)$calcrivalsainps,2,',','.').' name="calcrivalsainps"/>');
	echo('<input type="hidden" value='.number_format((float)$imponibile,2,',','.').' name="imponibile"/>');
	echo('<input type="hidden" value='.number_format((float)$totaledovuto,2,',','.').' name="totaledovuto"/>');
	echo('<input type="submit" value="Genera Pdf Fattura" name="submit"/></form>');
}

if($action == 'pdf')
{
	
	$calciva = $_POST['calciva'];
	$calcenasarco = $_POST['calcenasarco'];
	$calcritacconto = $_POST['calcritacconto'];
	$calccontributoinps = $_POST['calccontributoinps'];
	$calcrivalsainps = $_POST['calcrivalsainps'];
	$totaledovuto = $_POST['totaledovuto'];
	$imponibile = $_POST['imponibile'];
	$annomese = $_POST['annomese'];

	$anno = substr($annomese, 0, -2);
	$mese = substr($annomese, 4); 
	

$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->Image('euro-ellisse.png',10,6,60);
$pdf->addSociete( "EURO-PHARMA SRL",
                  "Via Beinette 8/d\n" .
                  "10127 Torino TO\n".
                  "P.IVA e C.F. 06328630014\n");
if($agente->partitaiva != NULL)
	$pdf->addFatturaNum( "FATTURA Nr");
else
	$pdf->addFatturaNum( "RICEVUTA Nr");

//$pdf->temporaire( "Devis temporaire" );
$pdf->addDate( "______________");
//$pdf->addClient("MIGLIORE Giuseppe Salvatore");
//$pdf->addPageNumber("1");
$pdf->addAgente($agente->cognome. " ". $agente->nome,$agente->indirizzo. " \n"."C.F. ".strtoupper($agente->codicefiscale). "\n".$agente->partitaiva);
//$pdf->addReglement("Chèque à réception de facture");
$pdf->addEcheance($mese.'/'.$anno);
//$pdf->addNumTVA("FR888777666");
//$pdf->addReference("Devis ... du ....");
$cols=array( ""  => 100,            
             "Importo" => 90);
$pdf->addCols( $cols);
$cols=array(  ""  => "L",            
             "Importo" => "R"
              );
$pdf->addLineFormat( $cols);
$pdf->addLineFormat($cols);

$y    = 109;
$line = array( 
               ""  => "IMPONIBILE\n\n",
              
               "Importo" => EURO." ".$imponibile."\n\n"
                );
$size = $pdf->addLine( $y, $line );
$y   += $size + 2;

if($calciva != 0)
{

	$line = array( 
               ""  => "IVA ".$agente->iva." %",
              
               "Importo" => EURO." ".$calciva
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 
}

if($calcenasarco != 0)
{

	$line = array( 
               ""  => "ENASARCO ".$agente->enasarco." %",
              
               "Importo" => EURO." ".$calcenasarco
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 
}

if($calcritacconto != 0)
{

	$line = array( 
               ""  => "RIT. ACC. ".$agente->ritacconto." %",
              
               "Importo" => EURO." ".$calcritacconto
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 
}

if($calccontributoinps != 0)
{

	$line = array( 
               ""  => "CASSA DI PREVIDENZA ".$agente->contributoinps." %",
              
               "Importo" => EURO." ".$calccontributoinps
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 
}

if($calcrivalsainps != 0)
{

	$line = array( 
               ""  => "RIVALSA INPS ".$agente->rivalsainps." %",
              
               "Importo" => EURO." ".$calcrivalsainps
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 
}



	$line = array( 
               ""  => "\n\nTOTALE FATTURA ",
              
               "Importo" => "\n\n".EURO." ".$totaledovuto
                );
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2; 



//$pdf->addCadreTVAs();
        
// invoice = array( "px_unit" => value,
//                  "qte"     => qte,
//                  "tva"     => code_tva );
// tab_tva = array( "1"       => 19.6,
//                  "2"       => 5.5, ... );
// params  = array( "RemiseGlobale" => [0|1],
//                      "remise_tva"     => [1|2...],  // {la remise s'applique sur ce code TVA}
//                      "remise"         => value,     // {montant de la remise}
//                      "remise_percent" => percent,   // {pourcentage de remise sur ce montant de TVA}
//                  "FraisPort"     => [0|1],
//                      "portTTC"        => value,     // montant des frais de ports TTC
//                                                     // par defaut la TVA = 19.6 %
//                      "portHT"         => value,     // montant des frais de ports HT
//                      "portTVA"        => tva_value, // valeur de la TVA a appliquer sur le montant HT
//                  "AccompteExige" => [0|1],
//                      "accompte"         => value    // montant de l'acompte (TTC)
//                      "accompte_percent" => percent  // pourcentage d'acompte (TTC)
//                  "Remarque" => "texte"              // texte
/*$tot_prods = array( array ( "px_unit" => 600, "qte" => 1, "tva" => 1 ),
                    array ( "px_unit" =>  10, "qte" => 1, "tva" => 1 ));
$tab_tva = array( "1"       => 19.6,
                  "2"       => 5.5);
$params  = array( "RemiseGlobale" => 1,
                      "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
                      "remise"         => 0,       // {montant de la remise}
                      "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
                  "FraisPort"     => 1,
                      "portTTC"        => 10,      // montant des frais de ports TTC
                                                   // par defaut la TVA = 19.6 %
                      "portHT"         => 0,       // montant des frais de ports HT
                      "portTVA"        => 19.6,    // valeur de la TVA a appliquer sur le montant HT
                  "AccompteExige" => 1,
                      "accompte"         => 0,     // montant de l'acompte (TTC)
                      "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
                  "Remarque" => "Avec un acompte, svp..." );

$pdf->addTVAs( $params, $tab_tva, $tot_prods);
$pdf->addCadreEurosFrancs(); */
$pdf->Output();
}


?>
