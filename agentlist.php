<?php
include('db.php');	
$query = $db->prepare('SELECT * FROM agenti');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
echo('<a href="index.php?section=addagent&action=add">Aggiungi nuovo agente</a>');
echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
foreach ($results as $row){
	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
}
echo('</table>');	
?>
