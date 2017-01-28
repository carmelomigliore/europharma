<?php
include('db.php');
include('agent.php');
$action = $_GET['action'];
$nome='';
$cognome='';
$codfisc='';
$partitaiva='';
$email='';
$email2='';
$tipocontratto = '';  //in realtà contiene il tipo di regime, nome resta tipo contratto per questioni di compatibilità
$tipoattivita = '';
$datainizio = '';
$datafine = '';
$dataperiodoprova = '';
$tel = '';
$indirizzo = '';
$note = '';
$iva=0;
$enasarco=0;
$ritacconto=0;
$rivalsainps=0;
$contributoinps=0;
$attivo = true;
$id=-1;

$label = "Inserisci Nuovo Agente";
	

if($action=='mod'){
	$label = "Modifica Agente";
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
	$rivalsainps=$result['rivalsainps'];
	$enasarco=$result['enasarco'];
	$contributoinps=$result['contributoinps'];
	$iva = $result['iva'];
	$tipocontratto = $result['tipocontratto'];  
	$tipoattivita = $result['tipoattivita'];
	$datainizio = $result['datainiziocontratto'];
	$datafine = $result['datafinecontratto'];
	$dataperiodoprova = $result['dataperiodoprova'];
	$tel = $result['telefono'];
	$indirizzo = $result['indirizzo'];
	$note = $result['note'];
	$cap = $result['cap'];
	$citta = $result['citta'];
	$provincia = $result['provincia'];
	$attivo = $result['attivo'];
	$email2 = $result['email2'];
}

if($action == 'add' || $action == 'mod'){
	$selectedregimeempty = $tipocontratto==''?'selected':'';
	$selectedregimeminimi = $tipocontratto=='MINIMI'?'selected':'';
	$selectedregimeforfettario = $tipocontratto=='FORFETTARIO'?'selected':'';
	$selectedisf = $tipoattivita=='I.S.F.'?'selected':'';
	$selectedagente = $tipoattivita=='Agente'?'selected':'';
	$selectedconsulente = $tipoattivita=='Consulente'?'selected':'';
	$selectedcapoarea = $tipoattivita=='CapoArea'?'selected':'';
	$selecteddirettore = $tipoattivita=='DirettoreItalia'?'selected':'';
	$checkedattivo = $attivo?'checked':'';
	
	
	if($action=='add')
		echo('<form method="POST" action="index.php?section=addagent&action=insert">');
	else
		echo('<form method="POST" action="index.php?section=addagent&action=update&id='.$id.'">');
		echo('<div class="caricodati" align="center" style="width:600px;"><div id="portfolio" class="container"><div class="title">
		<br>	<h1>'.$label.'</h1>
		</div>
			<table >
				<tr> ');
	echo('<td>');
	echo('Nome: </td><td><input type="text" name="nome" value="'.$nome.'" required="required">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Cognome: </td><td><input type="text" name="cognome" value="'.$cognome.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Codice fiscale: </td><td><input type="text" name="codicefiscale" value="'.$codfisc.'" pattern="^[a-zA-Z]{6}[0-9]{2}[a-zA-Z][0-9]{2}[a-zA-Z][0-9]{3}[a-zA-Z]-?$" required="required">');
	echo('</td></tr>');	
	echo('<tr> <td>');
	echo('Partita IVA: </td><td><input type="text" value="'.$partitaiva.'" name="partitaiva">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('E-Mail: </td><td><input type="email" name="email" value="'.$email.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('E-Mail alternativa: </td><td><input type="email" name="email2" value="'.$email2.'"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Indirizzo: </td><td><input type="text" name="indirizzo" value="'.$indirizzo.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('CAP: </td><td><input type="numeric" name="cap" value="'.$cap.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Città: </td><td><input type="text" name="citta" value="'.$citta.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Provincia: </td><td><input type="text" name="provincia" value="'.$provincia.'" pattern="[a-zA-Z]{2}" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Telefono: </td><td><input type="number" name="telefono" value="'.$tel.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Tipo Regime: </td><td><select name="tipocontratto"><option value="" '.$selectedregimeempty.'>-</option><option value="MINIMI" '.$selectedregimeminimi.'>MINIMI</option><option value="FORFETTARIO" '.$selectedregimeforfettario.'>FORFETTARIO</option></select><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Data inizio contratto: </td><td><input type="date" name="datainiziocontratto" value="'.$datainizio.'" required="required"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Data fine contratto: </td><td><input type="date" name="datafinecontratto" value="'.$datafine.'"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Data periodo prova: </td><td><input type="date" name="dataperiodoprova" value="'.$dataperiodoprova.'"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Tipo attività: </td><td><select name="tipoattivita"><option value="I.S.F." '.$selectedisf.'>I.S.F.</option><option value="Agente" '.$selectedagente.'>Agente</option><option value="Consulente" '.$selectedconsulente.'>Consulente</option><option value="CapoArea" '.$selectedcapoarea.'>CapoArea</option><option value="DirettoreItalia" '.$selecteddirettore.'>Direttore Rete Italia</option></select><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% IVA: </td><td><input type="number" value="'.$iva.'" step="any" min="0" name="iva">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Enasarco </td><td><input type="number" step="any" min="0" value="'.$enasarco.'" name="enasarco"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Ritenuta d\'acconto </td><td><input type="number" step="any" min="0" value="'.$ritacconto.'" name="ritacconto"><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Rivalsa INPS </td><td><input type="number" step="any" min="0" name="rivalsainps" value="'.$rivalsainps.'">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('% Contributo previdenziale </td><td><input type="number" step="any" min="0" name="contributoinps" value="'.$contributoinps.'">');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Attivo: </td><td><input type="checkbox" name="attivo" value="true" '.$checkedattivo.'><br>');
	echo('</td></tr>');
	echo('<tr> <td>');
	echo('Note: </td><td><textarea name="note" rows="4" cols="25">'.$note.'</textarea><br>');
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
	$email2=$_POST['email2'];
	$iva=$_POST['iva'];
	$enasarco=$_POST['enasarco'];
	$ritacconto=$_POST['ritacconto'];
	$rivalsainps=$_POST['rivalsainps'];
	$contributoinps=$_POST['contributoinps'];
	
	$tipocontratto = $_POST['tipocontratto'];
	$tipoattivita = $_POST['tipoattivita'];
	$datainizio = $_POST['datainiziocontratto'];
	$datafine = $_POST['datafinecontratto'];
	$dataperiodoprova = $_POST['dataperiodoprova'];
	$tel = $_POST['telefono'];
	$indirizzo = $_POST['indirizzo'];
	$citta = $_POST['citta'];
	$cap = $_POST['cap'];
	$provincia = $_POST['provincia'];
	$note = $_POST['note'];
	$attivo = $_POST['attivo']?$_POST['attivo']:"FALSE";
	if($action=='insert'){
		try{
		$agente = new Agent(NULL,NULL, $nome, $cognome, strtoupper($codfisc), $partitaiva, $email, $iva, $enasarco, $ritacconto, $contributoinps, $rivalsainps , $tipocontratto, $tipoattivita, $datainizio, $datafine, $dataperiodoprova, $tel, $indirizzo, $note, $cap, $citta, $provincia, $attivo, $email2);
		$agente->insertInDB($db);
		/*$query=$db->prepare('INSERT into agenti(nome, cognome, codicefiscale, partitaiva, email, iva, enasarco, ritacconto, contributoinps, rivalsainps) VALUES (:nome, :cognome, :codicefiscale, :partitaiva, :email, :iva, :enasarco, :ritacconto, :contributoinps, :rivalsainps)');
		$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codfisc, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, ':ritacconto' => $ritacconto, ':rivalsainps' => $rivalsainps, ':enasarco' => $enasarco, ':contributoinps' => $contributoinps));*/
		
		echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=agenti">Torna indietro</a>');
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
	else if($action=='update'){
		try{
		$id = $_GET['id'];
		$agente = new Agent(NULL, $id, $nome, $cognome, strtoupper($codfisc), $partitaiva, $email, $iva, $enasarco, $ritacconto, $contributoinps, $rivalsainps, $tipocontratto, $tipoattivita, $datainizio, $datafine, $dataperiodoprova, $tel, $indirizzo, $note, $cap, $citta, $provincia, $attivo, $email2);
		$agente->updateInDB($db);
		/*$query=$db->prepare('UPDATE agenti SET nome = :nome, cognome = :cognome, codicefiscale = :codicefiscale, partitaiva = :partitaiva, email = :email, iva = :iva, enasarco = :enasarco, ritacconto = :ritacconto, contributoinps = :contributoinps, rivalsainps = :rivalsainps WHERE id = :id');
	$query->execute(array(':nome' => $nome, ':cognome' => $cognome, ':codicefiscale' => $codfisc, ':partitaiva' => $partitaiva, ':email' => $email, ':iva' => $iva, ':ritacconto' => $ritacconto, ':rivalsainps' => $rivalsainps, ':enasarco' => $enasarco, ':contributoinps' => $contributoinps, ':id' => $id));*/
//$count = $query->rowCount();
echo('<br>Operazione eseguita con successo<br> <a href="index.php?section=agenti">Torna indietro</a>');
		}catch(Exception $pdoe){
			echo('Errore: '.$pdoe->getMessage());
		}
	}
}
