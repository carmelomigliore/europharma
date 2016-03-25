<?php
include('db.php');	
$query = $db->prepare('SELECT * FROM agenti');
$query->execute();
$results = $query->fetchAll(PDO::FETCH_ASSOC);

echo('<html>

	<head>
		<link rel="stylesheet" href="tableagentlist.css" type="text/css"/>	
	</head>

	<body>');

echo('<a href="index.php?section=addagent&action=add">Aggiungi nuovo agente</a>');
echo('<div  class="CSS_Table_Example" style="width:620px;height:150px;" > ');
echo('              <table >
                    <tr>
                        <td>
                            Nome
                        </td>
                        <td >
                            Cognome
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
foreach ($results as $row){
 echo('            <tr>
                        <td >
                            '.$row['nome'].'
                        </td>
                        <td>
                            '.$row['cognome'].'
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
	</body>	
</html>');

//echo('<table border="1"><tr><th>Nome</th><th>Cognome</th><th>Codice Fiscale</th><th>P.IVA</th><th>e-mail</th><th></th><th></th></tr>');
//foreach ($results as $row){
//	echo('<tr><td>'.$row['nome'].'</td><td>'.$row['cognome'].'</td><td>'.$row['codicefiscale'].'</td><td>'.$row['partitaiva'].'</td><td>'.$row['email'].'</td><td><a href="index.php?section=viewagent&id='.$row['id'].'">dettagli</a></td></tr>');
//}
//echo('</table>');	
?>
