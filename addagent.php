<?php
include('db.php');
$action = $_GET['action'];
$nome='';
$cognome='';
$codfisc='';
$partitaiva='';
$email='';
$iva=0;
$enasarco=0;
$ritacconto=0;
$tipoinps=0;
$contributoinps=0;
$id=-1;

if($action=='mod'){
	$id = $_GET['id'];
	$query = $db->prepare('SELECT * FROM agenti WHERE id = :id');
	$query->execute(array(':id' => $id));
	$result = $query->fetch(PDO::FETCH_ASSOC);
	$nome=$result['nome'];
	$cognome=$result['cognome'];
	$codfisc=$result['codicefiscale'];
	$partitaiva=$result['partitaiva'];
	$email=$result['email'];
	$ritacconto=$result['ritacconto'];
	$tipoinps=$result['tipoinps'];
	$enasarco=$result['enasarco'];
	$contributoinps=$result['contributoinps'];
}

if($action == 'add' || $action == 'mod'){
	if($action=='add')
		echo('<form method="POST" action="index.php?section=addagent&action=insert">');
	else
		echo('<form method="POST" action="index.php?section=addagent&action=update&id='.$id.'">');
	echo('<div align="center" class="CSS_Table_Example" style="width:600px;height:150px;">
			<table >
				<tr> ');
	echo('<td>');
	echo('Nome: </td><td><input type="text" name="nome" value="'.$nome.'" required="required">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Cognome: </td><td><input type="text" name="cognome" value="'.$cognome.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Codice fiscale: </td><td><input type="text" name="codicefiscale" value="'.$codfisc.'" pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]$" required="required">');
	echo('</td></tr>');	
	echo('<tr> <td>');
	echo('Partita IVA: </td><td><input type="text" value="'.$partitaiva.'" name="partitaiva">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('E-Mail: </td><td><input type="email" name="email" value="'.$email.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% IVA: </td><td><input type="number" value="'.$iva.'" name="iva">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Enasarco </td><td><input type="number" value="'.$enasarco.'" name="enasarco"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Ritenuta d\'acconto </td><td><input type="number" value="'.$ritacconto.'" name="ritacconto"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Tipo contributo INPS </td><td><select name="tipoinps" selected="'.$tipoinps.'"><option value="0">-</option><option value="1">Tipo1</option><option value="2">Tipo2</option></select>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Contributo INPS </td><td><input type="number" name="contributoinps" value="'.$contributoinps.'">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('<input type="submit" name="Invia">');
	echo('</td></tr>');
        echo('</table>
		</div>');
	echo('</form>');
}

if($action=='insert' || $action=='update'){
	$nome= $_POST['nome'];
	$cognome= $_POST['cognome'];
	$codfisc=$_POST['codicefiscale'];
	$partitaiva=$_POST['partitaiva'];
	$email=$_POST['email'];
	$iva=$_POST['iva'];
	$enasarco=$_POST['enasarco'];
	$ritacconto=$_POST['ritacconto'];
	$tipoinps=$_POST['tipoinps'];
	$contributoinps=$_POST['contributoinps'];
	if($action=='insert'){
		try{
		$query=$db->prepare('INSERT into agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, tipoinps) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :tipoinps)');
		$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codfisc, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, ':ritacconto' => $ritacconto, ':tipoinps' => $tipoinps, ':enasarco' => $enasarco, ':contributoinps' => $contributoinps));
		echo('Inserito nel DB');
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
	else if($action=='update'){
		try{
		$id = $_GET['id'];
		$query=$db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, tipoinps = :tipoinps WHERE id = :id');
	$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codfisc, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, ':ritacconto' => $ritacconto, ':tipoinps' => $tipoinps, ':enasarco' => $enasarco, ':contributoinps' => $contributoinps, ':id' => $id));
//$count = $query->rowCount();
echo('Modifica avvenuta con successo');
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
}
