<?php
include('db.php');
$action = $_GET['action'];

echo('<br><a href="index.php?section=agentlist&action=piva">Mostra solo agenti con P.IVA</a><br>');
echo('<br><a href="index.php?section=agentlist&action=nopiva">Mostra solo agenti senza P.IVA</a><br>');
echo('<br><a href="index.php?section=addagent&action=add">Aggiungi nuovo agente</a><br>');
echo('<div  class="CSS_Table_Example" style="width:820px;" > ');
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
                            P.IVA
                        </td>
			<td>
                            e-mail
                        </td>
			<td>
                            Dettagli
                        </td>
			<td>
				Modifica
			</td>
                    </tr>');

	$q = '';
	if($action == 'piva')
	{

		$q = "WHERE partitaiva <> ''";

	}
	else if($action == 'nopiva')
	{
		$q = "WHERE partitaiva IS NULL OR partitaiva = ''";
	}


try{
$query = $db->prepare('SELECT * FROM agenti '.$q.' ORDER BY cognome');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);
}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}


foreach ($results as $row){
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
                            '.$row['partitaiva'].'
                        </td>
			<td>
                            '.$row['email'].'
                        </td>
			<td>
                            <a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a>
                        </td>
			<td>
                            <a href="index.php?section=addagent&action=mod&id='.$row['id'].'">modifica</a>
                        </td>
                    </tr>');
                   
}
echo('              </table>
            </div>
	');

//echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
//foreach ($results as $row){
//	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
//}
//echo('</table>');	

?>
