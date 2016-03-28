<?php
include_once('db.php');
include_once('agent.php');
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
echo('<select>');
foreach($annomese as $am){
echo('<option value="am">'.$am'.</option>');
}
echo('</select>');

echo('<a href="index.php?section=fattura&action=generafattura&id='.$id.'">Genera Fattura</a>'.'<br>');

}

else if($action == 'generafattura')
{


echo('<table width="70%" align="center"><tr>');
echo('<td><p>Agente: '.$agente->nome.' '.$agente->cognome.'</p></td>');
echo('<td><p>Codice fiscale: '.$agente->codicefiscale.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Partita IVA: '.$agente->partitaiva.'</p></td>');
echo('<td><p>e-mail: '.$agente->email.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Indirizzo: '.$agente->indirizzo.'</p></td>');
echo('<td><p>Telefono: '.$agente->telefono.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Tipo di contratto: '.$agente->tipocontratto.'</p></td>');
echo('<td><p>Tipo attività: '.$agente->tipoattivita.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Data inizio: '.$agente->datainizio.'</p></td>');
echo('<td><p>Data fine: '.$agente->datafine.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Data periodo prova: '.$agente->dataperiodoprova.'</p></td>');
echo('<td><p>Telefono: '.$agente->telefono.'</p></td>');
echo('</tr><tr>');
echo('<td><p>% IVA: '.$agente->iva.'</p></td>');
echo('<td><p>% Enasarco: '.$agente->enasarco.'</p></td>');
echo('</tr><tr>');
echo('<td><p>% Ritenuta d\'acconto: '.$agente->ritacconto.'</p></td>');
echo('<td><p>% Contributo INPS: '.$agente->contributoinps.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Rivalsa INPS: '.$agente->rivalsainps.'</p></td>');
echo('<td><p>Indirizzo: '.$agente->indirizzo.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Note: '.$agente->note.'</p></td>');
echo('<td></td>');
echo('</tr><tr>');
echo('</table>');

}


?>
