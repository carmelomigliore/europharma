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
echo('<form method="POST" action="index.php?section=fattura&action=generafattura&id='.$id.'"><select name="selection">');
foreach($annomese as $am){
echo('<option value="'.$am['annomese'].'">'.$am['annomese'].'</option>');
}
echo('</select><input type="submit" value="Genera Fattura" name="submit"/></form>');


}

if($action == 'generafattura')
{
echo('id '.$id);
$annomese = $_POST['selection'];
$agente = Agent::getAgentFromDB($id,$db);

$calciva = 0;
$calcenasarco = 0;
$calcritacconto = 0;
$calccontributoinps = 0;
$calcrivalsainps = 0;
$totaledovuto = 0;

$agente->calculateSalary($db, $annomese, $calciva, $calcenasarco, $calcritacconto, $calccontributoinps, $calcrivalsainps, $totaledovuto);
echo('<table width="70%" align="center"><tr>');
echo('<td><p>Annomese: '.$annomese.'</p></td>');
echo('<td><p>Calciva: '.$calciva.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Calcenasarco: '.$calcenasarco.'</p></td>');
echo('<td><p>Contributo inps: '.$calccontributoinps.'</p></td>');
echo('</tr><tr>');
echo('<td><p>Rivalsa inps: '.$calcrivalsainps.'</p></td>');
echo('<td><p>Totale dovuto: '.$totaledovuto.'</p></td>');
echo('</tr><tr>');
echo('</table>'); 

}


?>
