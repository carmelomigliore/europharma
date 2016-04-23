<?php
include('db.php');	
$query = $db->prepare('SELECT * FROM prodotti');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);


echo('<a href="index.php?section=insertproduct&action=add">Aggiungi prodotto</a>');
echo('<div  class="CSS_Table_Example" style="width:60%;" > ');
echo('<table>
<tr><td>Nome</td><td>Sconto</td><td>Prezzo</td><td>Provvigione Default</td><td>Modifica</td></tr>');
foreach ($results as $row){
	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['sconto'].'</td><td>'.$row['prezzo'].'</td><td>'.$row['provvigionedefault'].'</td><td><a href="index.php?section=insertproduct&action=mod&id='.$row['id'].'">modifica</a></td></tr>');
}
echo('</table> </div>');	
?>
